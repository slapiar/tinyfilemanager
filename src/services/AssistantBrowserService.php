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
        $context = api_assistant_collect_files($this->root, $files, FM_IS_ADMIN ? 0 : ($c['assistant_max_files'] ?? 8), FM_IS_ADMIN ? 0 : ($c['assistant_max_file_bytes'] ?? 200000),
            FM_IS_ADMIN ? array() : ($c['assistant_allowed_extensions'] ?? array('php','md','txt','json','js','css','html','xml','yml','yaml','ini','sh','sql')));
        $response = api_http_post_json(rtrim($c['assistant_openai_base_url'] ?? 'https://api.openai.com/v1', '/') . '/chat/completions', array(
            'model' => $c['assistant_openai_model'] ?? 'gpt-4o-mini',
            'temperature' => $c['assistant_openai_temperature'] ?? 0.2,
            'stream' => false,
            'messages' => array(
                array('role' => 'system', 'content' => $c['assistant_system_prompt'] ?? 'Work only with the selected files. Paths in operations must match the supplied relative paths.'),
                array('role' => 'user', 'content' => $message . "\n\nProject file context:\n" . $context['context']),
            ),
        ), array('Authorization: Bearer ' . $c['assistant_openai_api_key']), 60);
        if ($response['status'] < 200 || $response['status'] >= 300) throw new RuntimeException($this->providerError($response));
        $data = json_decode($response['body'], true);
        $reply = $data['choices'][0]['message']['content'] ?? '';
        if (!is_string($reply) || trim($reply) === '') throw new RuntimeException('AI služba vrátila prázdnu odpoveď.');
        return $reply;
    }

    private function providerError(array $response)
    {
        $status = (int) $response['status'];
        if ($status === 0) return 'AI API: server nedostal HTTP odpoveď. Skontrolujte odchádzajúce HTTPS spojenie, DNS, TLS a časový limit.';
        $body = json_decode($response['body'], true);
        $error = isset($body['error']) && is_array($body['error']) ? $body['error'] : array();
        $code = isset($error['code']) && is_string($error['code']) ? $error['code'] : '';
        $type = isset($error['type']) && is_string($error['type']) ? $error['type'] : '';
        $messages = array(
            400 => 'API odmietlo formát alebo parametre požiadavky. Skontrolujte kompatibilitu nastaveného modelu s Chat Completions.',
            401 => 'API odmietlo overenie. Skontrolujte platnosť API kľúča v serverovom api.config.php.',
            403 => 'API zamietlo prístup. Skontrolujte oprávnenia projektu, kľúča a dostupnosť služby.',
            404 => 'Model alebo API endpoint nie je dostupný. Skontrolujte model a základnú URL v api.config.php.',
            429 => 'API obmedzilo požiadavku. Môže ísť o rýchlostný limit alebo limit účtu.',
        );
        $message = $messages[$status] ?? ($status >= 500 ? 'AI služba má dočasnú serverovú chybu. Skúste požiadavku neskôr.' : 'API požiadavku odmietlo.');
        if ($code === 'insufficient_quota' || $type === 'insufficient_quota') {
            $message = 'API účet nemá dostupnú kvótu. Skontrolujte kredit a limity API projektu.';
        } elseif ($code === 'rate_limit_exceeded' || $type === 'rate_limit_error') {
            $message = 'Prekročený rýchlostný limit API. Počkajte a zopakujte požiadavku.';
        } elseif ($code === 'model_not_found') {
            $message = 'Nastavený model neexistuje alebo k nemu API projekt nemá prístup.';
        }
        // Never display the provider message: it may echo credentials or file content.
        $safeCodes = array('invalid_api_key', 'insufficient_quota', 'rate_limit_exceeded', 'slow_down', 'model_not_found', 'unsupported_parameter', 'unsupported_value', 'context_length_exceeded', 'invalid_request_error');
        $detail = in_array($code, $safeCodes, true) ? '; ' . $code : '';
        if (isset($error['param']) && in_array($error['param'], array('temperature', 'model', 'messages', 'max_tokens', 'max_completion_tokens'), true)) {
            $detail .= '; parameter: ' . $error['param'];
        }
        return 'AI API (HTTP ' . $status . $detail . '): ' . $message;
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
                    if (!FM_IS_ADMIN && FM_FILE_EXTENSION && !in_array(strtolower(pathinfo($target, PATHINFO_EXTENSION)), array_map('trim', explode(',', strtolower(FM_FILE_EXTENSION))), true)) {
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
