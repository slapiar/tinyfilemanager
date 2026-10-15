#!/usr/bin/env python3
"""Exercise real release ZIPs and stale-ref protection in isolated local repos."""
import os
import shutil
import subprocess
import tempfile
from pathlib import Path
from zipfile import ZipFile

source = Path(__file__).resolve().parents[2]
def run(args, cwd, ok=True):
    result = subprocess.run(args, cwd=cwd, text=True, capture_output=True)
    if ok and result.returncode:
        raise AssertionError(result.stdout + result.stderr)
    return result

with tempfile.TemporaryDirectory(prefix='tfm-release-test-') as tmp:
    root = Path(tmp)
    remote, work, peer = (root / name for name in ('remote.git', 'work', 'peer'))
    run(['git', 'init', '--bare', str(remote)], root)
    work.mkdir()
    run(['git', 'init', '-b', 'master'], work)
    for key, value in [('user.name', 'Release Test'), ('user.email', 'release@example.invalid')]:
        run(['git', 'config', key, value], work)
    shutil.copy(source / 'release.sh', work / 'release.sh')
    (work / 'RELEASE_VERSION').write_text('1.0.0\n')
    (work / '.gitignore').write_text('releases/\napi.config.php\njoyee-bridge.config.php\n')
    (work / 'app.php').write_text('<?php echo "application";\n')
    configs = ['config.php', 'api.config.php', 'joyee-bridge.config.php']
    for name in configs:
        (work / name).write_text('<?php // repository configuration\n')
    (work / '.fm_usercfg').mkdir()
    (work / '.fm_usercfg/state.txt').write_text('runtime')
    run(['git', 'add', '.'], work)
    # Even force-tracked private configs must be excluded from ordinary updates.
    run(['git', 'add', '-f', *configs], work)
    run(['git', 'commit', '-m', 'fixture'], work)
    run(['git', 'remote', 'add', 'origin', str(remote)], work)
    run(['git', 'push', '-u', 'origin', 'master'], work)
    run(['bash', 'release.sh'], work)
    with ZipFile(work / 'releases/tinyfilemanager-1.0.0.zip') as archive:
        assert not set(configs).intersection(archive.namelist())
        assert '.fm_usercfg/state.txt' not in archive.namelist()
        assert 'app.php' in archive.namelist()
        production = root / 'production'
        production.mkdir()
        for name in configs:
            (production / name).write_text('server-owned')
        archive.extractall(production)
        assert all((production / name).read_text() == 'server-owned' for name in configs)
    run(['bash', 'release.sh', '--include-local-config'], work)
    with ZipFile(work / 'releases/tinyfilemanager-1.0.0.zip') as archive:
        assert set(configs).issubset(archive.namelist())
    # Rebuilding a default ZIP must remove configs from the preceding opt-in archive.
    run(['bash', 'release.sh'], work)
    with ZipFile(work / 'releases/tinyfilemanager-1.0.0.zip') as archive:
        assert not set(configs).intersection(archive.namelist())
    run(['git', 'clone', str(remote), str(peer)], root)
    for key, value in [('user.name', 'Release Test'), ('user.email', 'release@example.invalid')]:
        run(['git', 'config', key, value], peer)
    (peer / 'config.php').write_text('<?php // newer server configuration\n')
    run(['git', 'commit', '-am', 'remote change'], peer)
    run(['git', 'push'], peer)
    result = run(['bash', 'release.sh', 'patch'], work, ok=False)
    assert result.returncode and 'Synchronize before releasing' in result.stderr
    assert (work / 'RELEASE_VERSION').read_text().strip() == '1.0.0'
    assert not (work / 'releases/tinyfilemanager-1.0.1.zip').exists()
    run(['git', 'merge', '--ff-only', 'origin/master'], work)
    run(['bash', 'release.sh', 'patch'], work)
    assert (work / 'releases/tinyfilemanager-1.0.1.zip').exists()
    run(['git', 'remote', 'set-url', 'origin', str(root / 'missing.git')], work)
    result = run(['bash', 'release.sh'], work, ok=False)
    assert result.returncode and 'cannot verify remote state' in result.stderr
print('PASS: config exclusion, opt-in, server config preservation, clean archive rebuild, stale refs, synchronized release and failed fetch')
