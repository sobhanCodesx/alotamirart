#!/usr/bin/env python3
"""Publish reviewed main changes to the AloTamirArt signed HTTPS receiver."""
import hashlib, hmac, io, os, pathlib, re, subprocess, sys, urllib.request, urllib.error, zipfile
ROOT = pathlib.Path(__file__).resolve().parents[1]
ref = os.getenv("GITHUB_REF","")
sha = os.getenv("GITHUB_SHA","").lower()
before = os.getenv("BEFORE_SHA","").lower()
url = os.getenv("ALO_DEPLOY_URL","")
secret = os.getenv("MCP_API_TOKEN","")
def fail(message):
    sys.exit("Deployment refused: " + message)
def allowed(path):
    if not re.fullmatch(r"(api|app|bootstrap|classes|database|public|reqires|router|them|assets|css|js|images|img|fonts)/[A-Za-z0-9_./-]+",path) or len(path)>240:
        return False
    parts=path.split("/")
    if any(not p or p in (".","..") or p.startswith(".") for p in parts): return False
    if any(p.lower() in ("upload","uploads","storage","cache","log","logs","vendor","node_modules","backup","backups","secret","secrets") for p in parts): return False
    if pathlib.PurePosixPath(path).name.lower() in ("database.php","config.php","error_log"): return False
    return pathlib.PurePosixPath(path).suffix.lower() in (".php",".css",".js",".json",".html",".htm",".txt",".svg",".png",".jpg",".jpeg",".webp",".ico",".woff",".woff2")
if ref!="refs/heads/main" or not re.fullmatch("[a-f0-9]{40}",sha): fail("only main commit may deploy")
if not re.fullmatch("[a-f0-9]{40}",before) or before=="0"*40: fail("previous commit required")
if not url.startswith("https://") or not url.endswith("/api/deploy.php") or "@" in url or "?" in url: fail("invalid deploy HTTPS URL")
if len(secret)<32: fail("MCP_API_TOKEN missing")
subprocess.run(["git","cat-file","-e",before+"^{commit}"],cwd=ROOT,check=True)
deleted=subprocess.check_output(["git","diff","--name-only","--diff-filter=D",before,sha],cwd=ROOT).decode().splitlines()
if any(allowed(path) for path in deleted): fail("deletion requires reviewed manual migration")
changed=subprocess.check_output(["git","diff","--name-only","--diff-filter=ACMRT",before,sha],cwd=ROOT).decode().splitlines()
paths=[p for p in changed if allowed(p)]
if not paths:
    print("No production application files changed");sys.exit(0)
if len(paths)>400: fail("too many changed files")
# Single-item packages are opt-in for hosts whose security appliance rejects
# multi-file compressed PHP payloads. The PHP receiver remains HMAC-verified.
groups = [[p] for p in paths] if os.getenv("ALO_DEPLOY_SPLIT") == "1" else [paths]
auth = hmac.new(secret.encode(),b"alotamirart/deployment-auth/v1","sha256").hexdigest()
signkey = hmac.new(secret.encode(),b"alotamirart/deployment-package/v1","sha256").hexdigest()
acknowledged = 0
for group in groups:
    mem = io.BytesIO()
    with zipfile.ZipFile(mem,"w",zipfile.ZIP_DEFLATED,compresslevel=6) as archive:
        for path in group:
            file = ROOT/path
            if file.is_symlink() or not file.is_file() or file.stat().st_size>3*1024*1024:
                fail("invalid file "+path)
            archive.write(file,path)
    blob = mem.getvalue()
    if len(blob)>20*1024*1024: fail("package too large")
    sig = hmac.new(signkey.encode(),(sha+"\n"+hashlib.sha256(blob).hexdigest()).encode(),"sha256").hexdigest()
    req = urllib.request.Request(
        url,data=blob,method="POST",
        headers={"Authorization":"Bearer "+auth,
                 "X-Deploy-Sha":sha,"X-Deploy-Signature":sig,
                 "Content-Type":"application/octet-stream",
                 "User-Agent":"AloTamirArt-Github-Deploy/1"}
    )
    print("Deploying",len(group),"file(s),",len(blob),"bytes,",
          group[0] if len(group)==1 else "batch",sha[:12],flush=True)
    try:
        with urllib.request.urlopen(req,timeout=180) as response:
            import json
            result=json.loads(response.read(16000))
            if result.get("status")!="ok" or result.get("sha")!=sha or int(result.get("count",0))!=len(group):
                fail("Unexpected server verification response")
            acknowledged += len(group)
            print("Deployment acknowledged:",acknowledged,"of",len(paths),"files",flush=True)
    except urllib.error.HTTPError as e:
        fragment=e.read(1500).decode("utf-8","replace")
        label="BitNinja" if "bn403" in fragment or "Blocked Page" in fragment else "unknown"
        fail("hosting returned HTTP "+str(e.code)+" (blocker="+label+
             ", already-installed="+str(acknowledged)+"/"+str(len(paths))+")")
    except urllib.error.URLError:
        fail("connection failed (already-installed="+str(acknowledged)+
             "/"+str(len(paths))+")")
print("ALL_DEPLOYED_OK",acknowledged,"files")
