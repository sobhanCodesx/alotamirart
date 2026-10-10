#!/usr/bin/env python3
"""PlayNexus-style staged package deployer for AloTamiratchi cPanel.

Build a signed ZIP from reviewed main changes, upload it in <=512 KiB
multipart/form-data pieces, finalize, verify, apply in stages and assert health.

The receiving endpoint must already be installed on hosting once. No SSH/FTPS.
"""
from __future__ import annotations

import hashlib
import hmac
import io
import json
import math
import os
from pathlib import Path
import re
import subprocess
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
import uuid
import zipfile


ROOT = Path(__file__).resolve().parents[1]
API = os.getenv("ALO_DEPLOY_AGENT_URL", "https://www.alotamiratchi.ir/api/deployment-agent/").rstrip("/") + "/"
TOKEN = os.getenv("MCP_API_TOKEN", "")
SHA = os.getenv("GITHUB_SHA", "").lower()
REF = os.getenv("GITHUB_REF", "")
RUN = os.getenv("GITHUB_RUN_ID", "")
BEFORE = os.getenv("ALO_DEPLOY_BASE_SHA") or os.getenv("BEFORE_SHA", "")
CHUNK_SIZE = 512 * 1024
MAX_RETRIES = 3
from alo_release import changed_files, manifest as create_manifest, allowed


def fail(message: str):
    raise RuntimeError(message)


def git(*args: str) -> str:
    return subprocess.check_output(["git", *args], cwd=ROOT).decode("utf-8").strip()


def collect_changes() -> tuple[list[str], list[str]]:
    if REF != "refs/heads/main" or not re.fullmatch(r"[a-f0-9]{40}", SHA):
        fail("Only reviewed main commits may deploy")
    if not re.fullmatch(r"[a-f0-9]{40}", BEFORE) or BEFORE == "0" * 40:
        fail("Missing previous commit for signed release")
    try:
        git("merge-base", "--is-ancestor", BEFORE, SHA)
    except subprocess.CalledProcessError:
        fail("Previous production commit is not an ancestor of current main")
    return changed_files(BEFORE, SHA, ROOT)


def release(files: list[str], deleted: list[str]) -> bytes:
    payload = create_manifest(SHA, BEFORE, files, deleted, ROOT)
    data = json.dumps(payload, ensure_ascii=False, separators=(",", ":")).encode("utf-8")
    key = hmac.new(TOKEN.encode(), b"alotamirart/deployment-package/v1", hashlib.sha256).hexdigest()
    signature = hmac.new(key.encode(), data, hashlib.sha256).hexdigest()
    buffer = io.BytesIO()
    with zipfile.ZipFile(buffer, mode="w", compression=zipfile.ZIP_DEFLATED, compresslevel=6) as z:
        for path in files:
            z.write(ROOT / path, path)
        z.writestr("deployment-manifest.json", data)
        z.writestr("deployment-manifest.sig", signature)
    archive = buffer.getvalue()
    if len(archive) > 20 * 1024 * 1024:
        fail("Deployment ZIP exceeds 20 MiB")
    return archive


def request(
    method: str,
    action: str,
    payload: dict | None = None,
    *,
    multipart: tuple[dict[str, str], bytes] | None = None,
) -> dict:
    query = urllib.parse.urlencode({"action": action})
    url = API + "?" + query
    auth = hmac.new(TOKEN.encode(), b"alotamirart/deployment-auth/v1", hashlib.sha256).hexdigest()
    headers = {
        "Authorization": "Bearer " + auth,
        "Accept": "application/json",
        "X-Alo-Deploy-SHA": SHA,
        "X-Alo-Deploy-Run": RUN,
        "User-Agent": "AloTamiratchi-GitHub-Deployment/2.1",
    }
    if multipart is not None:
        fields, chunk = multipart
        boundary = "----AloDeploy" + uuid.uuid4().hex
        chunks = []
        for key, val in fields.items():
            chunks.append(
                f'--{boundary}\r\nContent-Disposition: form-data; name="{key}"\r\n\r\n{val}\r\n'.encode()
            )
        chunks.append((
            f'--{boundary}\r\nContent-Disposition: form-data; name="chunk"; '
            'filename="deployment.part"\r\nContent-Type: application/octet-stream\r\n\r\n'
        ).encode())
        chunks.append(chunk)
        chunks.append(f"\r\n--{boundary}--\r\n".encode())
        body = b"".join(chunks)
        headers["Content-Type"] = "multipart/form-data; boundary=" + boundary
    elif payload is not None:
        body = json.dumps(payload, separators=(",", ":")).encode()
        headers["Content-Type"] = "application/json"
    else:
        body = None
    for attempt in range(MAX_RETRIES):
        req = urllib.request.Request(url, data=body, headers=headers, method=method)
        try:
            with urllib.request.urlopen(req, timeout=90) as resp:
                try:
                    parsed = json.load(resp)
                except (ValueError, UnicodeDecodeError):
                    if action == "ready":
                        fail("HTTP 404: deployment receiver not bootstrapped")
                    fail("Deployment receiver did not return JSON: " + action)
                if not isinstance(parsed, dict):
                    fail("Deployment agent returned an invalid object")
                return parsed
        except urllib.error.HTTPError as exc:
            content = exc.read(3000).decode("utf-8", "replace")
            try:
                parsed = json.loads(content)
                message = parsed.get("error") or parsed.get("message") or f"HTTP {exc.code}"
            except json.JSONDecodeError:
                label = "BitNinja blocking page" if "bn403" in content or "Blocked Page" in content else "non-JSON response"
                message = f"HTTP {exc.code}: {label}"
            if exc.code not in (408, 425, 429, 500, 502, 503, 504) or attempt == MAX_RETRIES - 1:
                fail(f"Deployment agent {action} failed: {message}")
        except (urllib.error.URLError, TimeoutError) as exc:
            if attempt == MAX_RETRIES - 1:
                fail(f"Deployment agent unreachable: {type(exc).__name__}")
        time.sleep(2 ** attempt)
    fail("Deployment request retries exhausted")


def public_health() -> None:
    parsed = urllib.parse.urlsplit(API)
    origin = parsed.scheme + "://" + parsed.netloc
    for url in (origin + "/", origin + "/api/deploy.php"):
        req = urllib.request.Request(url, method="GET", headers={"User-Agent": "AloTamiratchi-Deploy-Health/2.1"})
        try:
            with urllib.request.urlopen(req, timeout=25) as resp:
                if url.endswith("/"):
                    body = resp.read(8192).lower()
                    if resp.status != 200 or (b"<html" not in body and b"<!doctype html" not in body):
                        fail("Homepage did not return an HTML shell")
                else:
                    fail("Unauthenticated deploy endpoint unexpectedly accepted GET")
        except urllib.error.HTTPError as exc:
            if not url.endswith("/api/deploy.php") or exc.code != 405:
                fail(f"Public health failed with HTTP {exc.code}: {url}")


def main() -> None:
    if len(TOKEN) < 32:
        fail("MCP_API_TOKEN GitHub repository secret is missing")
    if not RUN.isdigit():
        fail("Invalid GitHub Actions run id")
    # No manual server command: after the one-time ZIP extraction the receiver
    # advertises readiness. Until then the GitHub artifact remains available.
    try:
        ready = request("GET", "ready")
    except RuntimeError as exc:
        if "HTTP 404" in str(exc) or "Deployment disabled" in str(exc) or "HTTP 503" in str(exc):
            print("ALO_BOOTSTRAP_REQUIRED: manually extract the GitHub bootstrap ZIP once")
            return
        raise
    if ready.get("ready") is not True or ready.get("protocol_version") != 2:
        fail("Production receiver does not support release protocol v2")
    global BEFORE
    deployed_sha = ready.get("last_commit")
    if deployed_sha is not None:
        if not isinstance(deployed_sha, str) or not re.fullmatch(r"[a-f0-9]{40}", deployed_sha):
            fail("Invalid last successfully deployed Git commit")
        BEFORE = deployed_sha
    files, deleted = collect_changes()
    if not files and not deleted:
        print("No deployable website code changed since production base", BEFORE)
        return
    archive = release(files, deleted)
    count = math.ceil(len(archive) / CHUNK_SIZE)
    operation_id = ""
    print(f"Deploying {len(files)} changed and {len(deleted)} deleted app paths in {count} multipart chunks", flush=True)
    for i in range(count):
        chunk = archive[i * CHUNK_SIZE:(i + 1) * CHUNK_SIZE]
        meta = {
            "operation_id": operation_id,
            "chunk_index": str(i),
            "total_chunks": str(count),
            "size": str(len(archive)),
            "source_sha": SHA,
            "source_ref": REF,
            "run_id": RUN,
        }
        state = request("POST", "upload/chunk", multipart=(meta, chunk))
        got_id = str(state.get("id", ""))
        if not re.fullmatch(r"[a-f0-9]{32}", got_id) or (operation_id and got_id != operation_id):
            fail("Server returned an unexpected deployment operation id")
        operation_id = got_id
        if i == count - 1 or i % 10 == 0:
            print("Multipart progress:", i + 1, "of", count, flush=True)
    uploaded = request("POST", "upload/complete", {"operation_id": operation_id})
    if uploaded.get("status") not in ("uploaded", "verified"):
        fail("Server did not assemble the upload")
    verified = request("POST", operation_id + "/verify", {})
    preflight = verified.get("preflight") or []
    if verified.get("status") != "verified" or any(
        entry.get("status") == "error" for entry in preflight if isinstance(entry, dict)
    ):
        fail("Deployment preflight verification failed")
    print("Signed manifest, paths and hashes verified on server", flush=True)
    state = verified
    for _ in range(8):
        if state.get("status") == "completed":
            break
        state = request("POST", operation_id + "/apply", {})
        if state.get("status") == "failed":
            fail("Remote staged apply failed")
        print("Server stage:", state.get("stage"), flush=True)
    if state.get("status") != "completed":
        fail("Deployment did not complete")
    status = request("GET", operation_id + "/status")
    if status.get("status") != "completed":
        fail("Server lost deployment completion state")
    health = request("GET", "health")
    if health.get("status") != "ok" or health.get("commit") != SHA:
        fail("Authenticated post-deploy health check did not verify exact commit")
    if any(v.get("ok") is not True for v in (health.get("checks") or {}).values()):
        fail("Post-deploy server hash validation failed")
    public_health()
    print("PLAYNEXUS_STYLE_DEPLOY_VERIFIED_OK", SHA, len(files), "writes", len(deleted), "deletes", flush=True)


if __name__ == "__main__":
    try:
        main()
    except Exception as exc:
        print("::error::" + str(exc), file=sys.stderr)
        sys.exit(1)
