"""Isolated HTTP regression tests. Run: PHP_BIN=php python3 tests/regression/admin-permissions.py"""
import http.server
import threading
import http.cookiejar
import hashlib
import sqlite3
import json
import os
import re
from pathlib import Path
import shutil
import socket
import subprocess
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request

repo = Path(__file__).resolve().parents[2]
php = os.environ.get('PHP_BIN', 'php')
with tempfile.TemporaryDirectory(prefix='tfm-admin-test-') as temporary:
    root = Path(temporary)
    for source in repo.glob('*.php'):
        if source.name != 'config.php':
            shutil.copy(source, root / source.name)
    shutil.copytree(repo / 'src', root / 'src')
    shutil.copy(repo / 'translation.json', root / 'translation.json')
    (root / 'data/manager').mkdir(parents=True)
    (root / 'data/other').mkdir()
    (root / 'data/other/evidence.txt').write_text('visible to admin')
    (root / 'config.php').write_text('''<?php
$root_path = __DIR__ . '/data';
$root_url = '';
$state_storage_path = __DIR__ . '/state';
$auth_users = array('admin'=>password_hash('test-pass', PASSWORD_DEFAULT), 'manager'=>password_hash('test-pass', PASSWORD_DEFAULT), 'reader'=>password_hash('test-pass', PASSWORD_DEFAULT));
$manager_users = array('manager');
$readonly_users = array('admin', 'reader');
$upload_only_users = array('admin');
$bulk_actions_disabled_users = array('admin');
$directories_users = array('admin'=>'missing-directory', 'manager'=>'manager', 'reader'=>'other');
$user_manager_owners = array('reader'=>'manager', 'manager'=>'admin');
$global_readonly = true;
$use_auth = true;
''')
    (root / '.fm_usercfg').mkdir()
    (root / '.fm_usercfg' / (hashlib.md5(b'admin').hexdigest() + '.json')).write_text(json.dumps({'theme': 'light', 'list_density': 'normal', 'lang': 'sk'}))
    provider_requests = []
    class Provider(http.server.BaseHTTPRequestHandler):
        def do_POST(self):
            provider_requests.append(json.loads(self.rfile.read(int(self.headers['Content-Length']))))
            payload = json.dumps({'choices': [{'message': {'content': '{"summary":"Checked","operations":[]}'}}]}).encode()
            self.send_response(200)
            self.end_headers()
            self.wfile.write(payload)
        def log_message(self, *args): pass
    provider = http.server.ThreadingHTTPServer(('127.0.0.1', 0), Provider)
    threading.Thread(target=provider.serve_forever, daemon=True).start()
    (root / 'api.config.php').write_text("<?php $assistant_enabled=true; $assistant_openai_api_key='test-only'; $assistant_openai_base_url='http://127.0.0.1:" + str(provider.server_port) + "'; $assistant_root_path=__DIR__.'/Joyee';")
    # This fixture exists only in the temporary copy served on loopback.
    (root / 'fixture.php').write_text('''<?php
session_name('filemanager'); session_start();
$_SESSION['filemanager']['logged'] = $_GET['user'];
$_SESSION['token'] = 'test-csrf-token';
if (isset($_GET['stale_settings'])) {
    $_SESSION['filemanager']['user_settings'] = array('fallback_logging' => false, 'display_defaults_version' => 1);
}
if (isset($_GET['enable_writes'])) {
    require __DIR__ . '/config.php';
    function fm_runtime_state_dir() { return __DIR__ . '/state'; }
    require __DIR__ . '/src/ConfigStore.php';
    $values = fm_config_store_load_scope('runtime_config', 'global');
    $values['global_readonly'] = false;
    fm_config_store_save_runtime_config($values);
}
echo 'ready';
''')
    with socket.socket() as probe:
        probe.bind(('127.0.0.1', 0))
        port = probe.getsockname()[1]
    base = f'http://127.0.0.1:{port}/'
    log = open(root / 'server.log', 'w+')
    server = subprocess.Popen([php, '-d', 'session.save_path=' + str(root), '-S', f'127.0.0.1:{port}', '-t', str(root)], stdout=log, stderr=log)
    clients = {}
    def request(user, query='', data=None, fixture=False):
        client = clients.setdefault(user, urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar())))
        url = base + ('fixture.php?user=' + user + '&' + query if fixture else 'tinyfilemanager.php?p=&' + query)
        payload = None if data is None else urllib.parse.urlencode(data).encode()
        req = urllib.request.Request(url, data=payload, headers={'X-Requested-With': 'XMLHttpRequest'} if data is not None else {})
        try:
            response = client.open(req, timeout=30)
        except urllib.error.HTTPError as error:
            response = error
        return response.status, response.read().decode()
    def save(user, name, original=None, role='standard', directory='manager', password='', owner='admin'):
        return request(user, 'admin_users_save=1', dict(token='test-csrf-token', mode='edit' if original else 'new', username=name,
            original_username=original or name, password=password, password2=password, access_type=role,
            directories=directory, manager_owner=owner, bulk_actions_enabled='1'))
    def state():
        raw = subprocess.check_output([php, '-r', 'include $argv[1]; echo json_encode(get_defined_vars());', str(root / 'config.php')], text=True)
        return json.loads(raw)
    try:
        for _ in range(100):
            try:
                request('admin', fixture=True)
                break
            except OSError:
                time.sleep(.05)
        else:
            raise AssertionError('PHP server did not start')
        for user in ('manager', 'reader'):
            request(user, fixture=True)
        status, body = request('admin', 'p=other')
        assert status == 200 and 'evidence.txt' in body, body[-1200:]
        assert 'data-bs-theme="dark"' in body and 'fm-density-compact' in body
        status, body = request('reader', 'p=other')
        assert 'value="manager">manager (manažér)</option>' in body
        assert 'data-bs-theme="dark"' in body and 'fm-density-compact' in body
        # AI starts in the assigned home, with the same scope at execution time.
        status, body = request('reader', 'p=other&assistant_browser=1')
        assert 'name="assistant_path" value="other"' in body, body[-600:]
        assert 'value="other/evidence.txt"' in body
        assert not (root / 'Joyee').exists()
        status, body = request('reader', 'p=other&assistant_browser=1&ajp=manager')
        assert 'nie je pridelený' in body
        ai = {'token': 'test-csrf-token', 'assistant_run': '1', 'assistant_message': 'Inspect', 'assistant_files[]': 'other/evidence.txt'}
        status, body = request('reader', 'p=other&assistant_browser=1', ai)
        assert 'Checked' in body and len(provider_requests) == 1, body[-800:]
        ai['assistant_files[]'] = 'manager/secret.txt'
        (root / 'data/manager/secret.txt').write_text('private')
        status, body = request('reader', 'p=other&assistant_browser=1', ai)
        assert len(provider_requests) == 1
        ai['token'] = 'wrong'
        status, body = request('reader', 'p=other&assistant_browser=1', ai)
        assert status == 403
        ai = {'token': 'test-csrf-token', 'assistant_apply': '1', 'assistant_require_confirmation': '0', 'assistant_plan_json': json.dumps({'operations': [{'action':'write','path':'other/blocked.txt','content':'blocked'}]})}
        request('reader', 'p=other&assistant_browser=1', ai)
        assert not (root / 'data/other/blocked.txt').exists()
        request('admin', 'p=other&assistant_browser=1', ai)
        assert (root / 'data/other/blocked.txt').read_text() == 'blocked'
        status, body = request('admin', 'p=other')
        assert 'capture="environment"' in body and 'Odfotiť a nahrať' in body and 'Nahrať zo zariadenia' in body
        assert body.count('fa-cloud-upload') >= 2
        status, body = request('reader', 'p=other&chat_action=fetch&with=manager')
        assert status == 200 and json.loads(body)['ok'], body
        status, body = request('manager', 'p=manager&chat_action=fetch&with=reader')
        assert status == 200 and json.loads(body)['ok'], body
        # A later explicit display preference is not forced back on each request.
        status, body = request('admin', '', {'ajax': '1', 'type': 'settings', 'token': 'test-csrf-token', 'js-language': 'sk', 'js-theme-3': 'light', 'js-list-density': 'normal'})
        status, body = request('admin', 'p=other')
        assert 'data-bs-theme="light"' in body and 'fm-density-normal' in body, body[:300]
        # Recover from a former session-only fallback and retain the new value.
        request('admin', 'stale_settings=1', fixture=True)
        settings = {'ajax': '1', 'type': 'settings', 'token': 'test-csrf-token', 'js-language': 'sk', 'js-theme-3': 'light', 'js-list-density': 'normal', 'js-fallback-log-enabled': 'true'}
        status, body = request('admin', '', settings)
        assert status == 200 and json.loads(body)['success'], body
        status, body = request('admin', 'p=other&settings=1')
        checkbox = re.search(r'<input[^>]+id="js-fallback-log-enabled"[^>]*>', body).group()
        assert 'checked' in checkbox, checkbox
        # Also survives a fresh authenticated session, not just a page reload.
        clients.pop('admin')
        request('admin', fixture=True)
        status, body = request('admin', 'p=other&settings=1')
        assert 'checked' in re.search(r'<input[^>]+id="js-fallback-log-enabled"[^>]*>', body).group()
        settings.pop('js-fallback-log-enabled')
        status, body = request('admin', '', settings)
        assert json.loads(body)['success'], body
        status, body = request('admin', 'p=other&settings=1')
        assert 'checked' not in re.search(r'<input[^>]+id="js-fallback-log-enabled"[^>]*>', body).group()
        status, body = request('admin', 'admin_users_modal=edit&user=reader')
        assert 'name="original_username"' in body and 'minlength="2"' in body, body[:300]
        username_input = re.search(r'<input[^>]+id="admin-username"[^>]*>', body).group()
        assert 'readonly' not in username_input
        status, body = save('admin', 'xy', password='test-pass')
        assert status == 200 and json.loads(body)['ok'], body
        status, body = save('admin', 'x', password='test-pass')
        assert status == 400, body
        request('login-test', fixture=True)
        status, body = request('login-test', '', dict(token='test-csrf-token', fm_usr='xy', fm_pwd='test-pass'))
        assert 'name="fm_usr"' not in body and 'manager' in body, body[-500:]
        assert 'data-bs-theme="dark"' in body and 'fm-density-compact' in body
        status, body = request('admin', 'p=other', dict(token='test-csrf-token', newfilename='admin-write', newfile='folder'))
        assert (root / 'data/other/admin-write').is_dir(), body[-500:]
        (root / '.fm_usercfg').mkdir(exist_ok=True)
        profile = root / '.fm_usercfg' / (hashlib.md5(b'reader').hexdigest() + '.json')
        profile.write_text(json.dumps({'theme': 'dark', 'lang': 'sk'}))
        chat = sqlite3.connect(root / 'state/chat.sqlite')
        chat.execute('CREATE TABLE IF NOT EXISTS fm_chat_messages (id INTEGER PRIMARY KEY AUTOINCREMENT, sender TEXT NOT NULL, recipient TEXT NOT NULL, message TEXT NOT NULL, created_at INTEGER NOT NULL)')
        chat.execute("INSERT INTO fm_chat_messages(sender, recipient, message, created_at) VALUES ('manager','reader','Keep this conversation',1)")
        chat.commit()
        before = state()['auth_users']['reader']
        status, body = save('admin', 'rd', original='reader', role='read only', owner='manager')
        assert status == 200 and json.loads(body)['ok'], body
        current = state()
        assert 'reader' not in current['auth_users'] and current['auth_users']['rd'] == before
        assert chat.execute("SELECT recipient FROM fm_chat_messages WHERE message='Keep this conversation'").fetchone()[0] == 'rd'
        profile_check = subprocess.check_output([php, '-r', "include $argv[1]; function fm_runtime_state_dir() { global $state_storage_path; return $state_storage_path; } require $argv[2]; echo json_encode(fm_config_store_load_scope('ui_preferences','rd'));", str(root / 'config.php'), str(root / 'src/ConfigStore.php')], text=True)
        assert json.loads(profile_check)['theme'] == 'dark'
        assert 'rd' in current['readonly_users'] and current['directories_users']['rd'] == 'manager'
        status, body = save('admin', 'xy', original='rd')
        assert status == 400 and 'rd' in state()['auth_users'], body
        status, body = save('manager', 'zz', original='rd')
        assert status == 403, body
        status, body = save('admin', 'mg', original='manager', role='manager')
        assert status == 200 and json.loads(body)['ok'], body
        assert 'mg' in state()['manager_users']
        assert state()['user_manager_owners']['rd'] == 'mg'
        assert chat.execute("SELECT sender FROM fm_chat_messages WHERE message='Keep this conversation'").fetchone()[0] == 'mg'
        # Two separate user scopes: a manager mutation must invalidate admin's tree.
        request('mg', 'enable_writes=1', fixture=True)
        # Reject a mixed AI plan before its first write; foreign and symlink targets stay untouched.
        operations = [{'action':'write','path':'manager/first.txt','content':'ok'}, {'action':'write','path':'other/foreign.txt','content':'no'}]
        ai = {'token':'test-csrf-token','assistant_apply':'1','assistant_require_confirmation':'0','assistant_plan_json':json.dumps({'operations':operations})}
        request('mg','p=manager&assistant_browser=1',ai)
        assert not (root / 'data/manager/first.txt').exists()
        assert not (root / 'data/other/foreign.txt').exists()
        (root / 'data/manager/link').symlink_to(root / 'data/other', target_is_directory=True)
        operations[1]['path'] = 'manager/link/foreign.txt'
        ai['assistant_plan_json'] = json.dumps({'operations':operations})
        request('mg','p=manager&assistant_browser=1',ai)
        assert not (root / 'data/manager/first.txt').exists()
        (root / 'data/manager/link').unlink()
        ai['assistant_plan_json'] = json.dumps({'operations':operations[:1]})
        request('mg','p=manager&assistant_browser=1',ai)
        assert (root / 'data/manager/first.txt').read_text() == 'ok'
        status, body = request('admin', 'p=manager')
        assert 'new-shared-folder' not in body
        status, body = request('mg', 'p=manager', dict(token='test-csrf-token',newfilename='new-shared-folder',newfile='folder'))
        assert (root / 'data/manager/new-shared-folder').is_dir(), body[:500]
        status, body = request('admin', 'p=manager')
        assert 'new-shared-folder' in body, body[:500]
        status, body = save('admin', 'boss', original='admin', directory='')
        assert status == 200 and json.loads(body)['ok'], body
        assert state()['admin_identity']['username'] == 'boss'
        status, body = request('admin', '', {'ajax': '1', 'type': 'settings_clear_fallback_log', 'token': 'test-csrf-token'})
        assert status == 200 and json.loads(body)['success'], body
        # The existing administrator session follows its new login.
        status, body = save('admin', 'ok', password='test-pass', owner='boss')
        assert status == 200 and json.loads(body)['ok'], body
        status, body = request('admin', 'p=other')
        assert 'evidence.txt' in body, body[:500]
        print('PASS: admin visibility, editable name, 2-character names, rename/password/roles, collision rejection, manager boundary, cross-scope folder refresh, admin self-rename/session, dark/compact profile migration and personal overrides, manager chat across different directories, fallback toggle on/off and session recovery')
    except Exception:
        log.flush()
        print((root / 'server.log').read_text()[-4500:])
        raise
    finally:
        provider.shutdown()
        provider.server_close()
        server.terminate()
        server.wait(timeout=5)
        log.close()
        if 'chat' in locals(): chat.close()
