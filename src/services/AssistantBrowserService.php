<?php
/** Session-scoped assistant: the browser and execution use the same user permissions. */
class TFM_AssistantBrowserService
{
    private $root;
    private $config;

    public function __construct()
    {
        if (!defined('TFM_API_FUNCTIONS_ONLY')) define('TFM_API_FUNCTIONS_ONLY', true);
        require_once dirname(__DIR__, 2) . '/api.core.php';
        $this->root = api_real_root(FM_ROOT_PATH);
        $this->config = (static function ($file) {
            if (is_readable($file)) require $file;
            return get_defined_vars();
        })(dirname(__DIR__, 2) . '/api.config.php');
    }

    public function path($relative, $allowParent = false)
    {
        global $fm_user_allowed_dirs;
        $relative = api_normalize_relative_path($relative);
        if (strpos($relative, "\0") !== false || preg_match('/^[a-zA-Z]:/', $relative)) {
            throw new RuntimeException('Neplatná cesta.');
        }
        $target = $this->root . ($relative === '' ? '' : '/' . $relative);
        // Check the nearest existing ancestor as well as the final resolved path.
        $ancestor = $target;
        $tail = array();
        while (!file_exists($ancestor) && !is_link($ancestor)) {
            array_unshift($tail, basename($ancestor));
            $ancestor = dirname($ancestor);
        }
        $resolved = realpath($ancestor);
        if ($resolved === false) throw new RuntimeException('Neplatný odkaz na súbor.');
        $resolved = str_replace('\\', '/', $resolved) . (empty($tail) ? '' : '/' . implode('/', $tail));
        if (!fm_is_path_inside($resolved, $this->root)) throw new RuntimeException('Prístup mimo pracovného priestoru nie je povolený.');
        if (!FM_IS_ADMIN && !empty($fm_user_allowed_dirs)) {
            $allowed = false;
            foreach ($fm_user_allowed_dirs as $directory) {
                $base = realpath($directory);
                if ($base === false || !fm_is_path_inside($base, $this->root)) continue;
                if (fm_is_path_inside($resolved, $base) || ($allowParent && fm_is_path_inside($base, $resolved))) {
                    $allowed = true;
                    break;
                }
            }
            if (!$allowed) throw new RuntimeException('Tento priečinok nie je pridelený vášmu účtu.');
        }
        return $resolved;
    }

    private function inspectTree($path)
    {
        if (is_link($path)) throw new RuntimeException('AI operácie nad symbolickými odkazmi nie sú podporované.');
        if (!is_dir($path)) return;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $entry) {
            if ($entry->isLink()) throw new RuntimeException('Priečinok obsahuje symbolický odkaz.');
        }
    }

    public function plan($message, array $files)
    {
        if (FM_UPLOAD_ONLY) throw new RuntimeException('Účet určený iba na nahrávanie nemôže posielať obsah súborov AI.');
        $c = $this->config;
        if (empty($c['assistant_enabled']) || empty($c['assistant_openai_api_key'])) {
            throw new RuntimeException('AI asistent zatiaľ nie je nakonfigurovaný.');
        }
        foreach ($files as $file) $this->path($file);
        $context = api_assistant_collect_files($this->root, $files, $c['assistant_max_files'] ?? 8, $c['assistant_max_file_bytes'] ?? 200000,
            $c['assistant_allowed_extensions'] ?? array('php','md','txt','json','js','css','html','xml','yml','yaml','ini','sh','sql'));
        $response = api_http_post_json(rtrim($c['assistant_openai_base_url'] ?? 'https://api.openai.com/v1', '/') . '/chat/completions', array(
            'model' => $c['assistant_openai_model'] ?? 'gpt-4o-mini',
            'temperature' => $c['assistant_openai_temperature'] ?? 0.2,
            'stream' => false,
            'messages' => array(
                array('role' => 'system', 'content' => $c['assistant_system_prompt'] ?? 'Work only with the selected files. Paths in operations must match the supplied relative paths.'),
                array('role' => 'user', 'content' => $message . "\n\nProject file context:\n" . $context['context']),
            ),
        ), array('Authorization: Bearer ' . $c['assistant_openai_api_key']), 60);
        if ($response['status'] < 200 || $response['status'] >= 300) throw new RuntimeException('AI služba požiadavku nespracovala.');
        $data = json_decode($response['body'], true);
        $reply = $data['choices'][0]['message']['content'] ?? '';
        if (!is_string($reply) || trim($reply) === '') throw new RuntimeException('AI služba vrátila prázdnu odpoveď.');
        return $reply;
    }

    public function apply(array $operations, $requireConfirmation, array $confirmed)
    {
        if (FM_READONLY || FM_UPLOAD_ONLY) throw new RuntimeException('Vaše oprávnenia neumožňujú meniť súbory cez AI.');
        // Preflight the entire selected plan before the first mutation.
        foreach ($operations as $index => $operation) {
            if ($requireConfirmation && !in_array((int) $index, $confirmed, true)) continue;
            $action = $operation['action'] ?? 'write';
            if (!in_array($action, array('write','mkdir','delete','move','copy'), true)) throw new RuntimeException('Nepodporovaná operácia.');
            if (FM_MANAGER && $action === 'delete') throw new RuntimeException('Manažér nemá oprávnenie mazať.');
            $keys = in_array($action, array('move','copy'), true) ? array('from','to') : array('path');
            foreach ($keys as $key) {
                if (empty($operation[$key]) || !is_string($operation[$key])) throw new RuntimeException('Chýba cesta operácie.');
                $target = $this->path($operation[$key]);
                if ($target === $this->root) throw new RuntimeException('Operácia nad koreňovým priečinkom nie je povolená.');
                $this->inspectTree($target);
                if ($action === 'write' || ($key === 'to' && !is_dir($this->path($operation['from'])))) {
                    if (FM_FILE_EXTENSION && !in_array(strtolower(pathinfo($target, PATHINFO_EXTENSION)), array_map('trim', explode(',', strtolower(FM_FILE_EXTENSION))), true)) {
                        throw new RuntimeException('Nepovolená prípona súboru.');
                    }
                }
            }
        }
        try {
            return api_assistant_apply_operations($this->root, $operations, $requireConfirmation, $confirmed);
        } finally {
            fm_search_index_mark_dirty('assistant_apply');
        }
    }
}
