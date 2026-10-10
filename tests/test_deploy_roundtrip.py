#!/usr/bin/env python3
"""Real local HTTP/PHP integration for signed deployment (no production contact)."""
from __future__ import annotations

import hashlib
import hmac
import json
import os
from pathlib import Path
import shutil
import socket
import subprocess
import sys
import tempfile
import time
import urllib.request

SOURCE=Path(__file__).resolve().parents[1]

def git(root,*args):
    return subprocess.check_output(["git",*args],cwd=root).decode().strip()

def main():
    with tempfile.TemporaryDirectory(prefix="alo-release-http-") as base:
        root=Path(base)/"site"
        root.mkdir()
        for relative in ("api/deployment-agent/index.php","app/Support/env.php"):
            target=root/relative
            target.parent.mkdir(parents=True,exist_ok=True)
            shutil.copy2(SOURCE/relative,target)
        (root/"api/fixture.php").write_text("<?php echo 'first';",encoding="utf-8")
        (root/"config").mkdir()
        (root/"config/old.php").write_text("<?php echo 'delete me';",encoding="utf-8")
        (root/"ci-router.php").write_text("""<?php
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
if ($path === '/') {
    header('Content-Type: text/html');
    echo '<!doctype html><html><body>CI app shell</body></html>';
    return true;
}
if ($path === '/api/deploy.php') {
    http_response_code(405);
    return true;
}
return false;
""",encoding="utf-8")
        git(root,"init","-q")
        git(root,"config","user.name","CI")
        git(root,"config","user.email","ci@example.test")
        git(root,"add",".")
        git(root,"commit","-qm","initial deploy source")
        before=git(root,"rev-parse","HEAD")
        (root/"api/fixture.php").write_text("<?php echo 'updated';",encoding="utf-8")
        (root/"config/old.php").unlink()
        (root/".htaccess").write_text("RewriteEngine On\n",encoding="utf-8")
        git(root,"add","-A")
        git(root,"commit","-qm","change, add root and delete")
        after=git(root,"rev-parse","HEAD")
        with socket.socket() as s:
            s.bind(("127.0.0.1",0))
            port=s.getsockname()[1]
        key="ci-test-deployment-token-0123456789abcdef0123456789abcdef"
        env=os.environ.copy()
        env.update({"MCP_API_TOKEN":key,"ALO_DEPLOY_ENABLED":"0"})
        server=subprocess.Popen(["php","-S",f"127.0.0.1:{port}","-t",str(root),str(root/"ci-router.php")],
            env=env,stdout=subprocess.DEVNULL,stderr=subprocess.PIPE)
        try:
            url=f"http://127.0.0.1:{port}/"
            for attempt in range(40):
                try:
                    with urllib.request.urlopen(url,timeout=1) as response:
                        if response.status==200:break
                except Exception:
                    if server.poll() is not None:
                        raise RuntimeError("PHP test server exited before responding")
                    time.sleep(.15)
            else:
                raise RuntimeError("PHP test server unavailable")
            env.update({
                "ALO_SOURCE_ROOT":str(root),
                "ALO_DEPLOY_AGENT_URL":f"http://127.0.0.1:{port}/api/deployment-agent/",
                "GITHUB_REF":"refs/heads/main",
                "GITHUB_SHA":after,
                "GITHUB_RUN_ID":"123456789",
                "BEFORE_SHA":before,
            })
            process=subprocess.run([sys.executable,str(SOURCE/"scripts/alo_deploy_playnexus.py")],
                env=env,cwd=SOURCE,capture_output=True,text=True,timeout=90)
            if process.returncode:
                print(process.stdout)
                print(process.stderr,file=sys.stderr)
                raise RuntimeError("Signed PHP HTTP deployment integration failed")
            assert "PLAYNEXUS_STYLE_DEPLOY_VERIFIED_OK" in process.stdout, process.stdout
            assert (root/"api/fixture.php").read_text()=="<?php echo 'updated';"
            assert not (root/"config/old.php").exists()
            assert (root/".htaccess").is_file()
            derived=hmac.new(key.encode(),b"alotamirart/deployment-auth/v1",hashlib.sha256).hexdigest()
            ready=urllib.request.Request(
                f"http://127.0.0.1:{port}/api/deployment-agent/?action=ready",
                headers={"Authorization":"Bearer "+derived})
            with urllib.request.urlopen(ready,timeout=5) as response:
                reply=json.load(response)
            assert reply["ready"] is True and reply["last_commit"]==after,reply
            print("SIGNED_RELEASE_ROUNDTRIP_OK",after)
        finally:
            server.terminate()
            try:
                server.communicate(timeout=5)
            except subprocess.TimeoutExpired:
                server.kill()
                server.communicate()

if __name__=="__main__":
    main()
