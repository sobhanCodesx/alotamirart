#!/usr/bin/env python3
"""Deploy reviewed, allowlisted application files over verified TLS FTP.

Used only when the HMAC-authenticated HTTP upload is incorrectly rejected by
the hosting WAF. Credentials are supplied by GitHub Actions encrypted secrets;
never passed in the repository or written into logs.
"""
from __future__ import annotations

import ftplib
import os
from pathlib import Path
import re
import secrets
import ssl


def require(name: str) -> str:
    value = os.getenv(name, "").strip()
    if not value:
        raise RuntimeError(f"{name} is not configured in GitHub Actions secrets")
    return value


def allowed_relative(path: str) -> list[str]:
    parts = path.split("/")
    if (
        not parts or any(not p or p in (".", "..") or p.startswith(".") for p in parts)
        or not re.fullmatch(r"[A-Za-z0-9_./-]{1,240}", path)
    ):
        raise RuntimeError("Invalid FTPS upload path")
    return parts


def verify_site_root(ftp: ftplib.FTP_TLS) -> None:
    # An FTP account sometimes starts in public_html, sometimes above it.
    # Never deploy unless the existing application's receiver is confirmed.
    data = bytearray()
    def collect(chunk: bytes) -> None:
        if len(data) <= 65536:
            data.extend(chunk[: 65537 - len(data)])
    ftp.retrbinary("RETR api/deploy.php", collect, blocksize=8192)
    if b"AloTamirArt deployment receiver" not in data and b"AloTamirArt deployment" not in data:
        raise RuntimeError("FTP root is not the verified AloTamirArt document root")
    probe = bytearray()
    ftp.retrbinary("RETR index.php", lambda chunk: probe.extend(chunk[:4096-len(probe)]) if len(probe)<4096 else None)
    if not probe:
        raise RuntimeError("FTP target root has no readable website front controller")


def locate_root(ftp: ftplib.FTP_TLS) -> str:
    start = ftp.pwd()
    configured = os.getenv("ALO_FTPS_ROOT", "").strip()
    if configured:
        if configured not in (".", "/"):
            if any(x in (".", "..", "") or x.startswith(".") for x in configured.strip("/").split("/")):
                raise RuntimeError("ALO_FTPS_ROOT contains an unsafe directory segment")
        candidates = [configured]
    else:
        candidates = [".", "public_html", "www"]
    for candidate in candidates:
        try:
            ftp.cwd(start)
            ftp.cwd(candidate)
            verify_site_root(ftp)
            return ftp.pwd()
        except ftplib.all_errors + (RuntimeError,):
            continue
    raise RuntimeError(
        "FTP account cannot locate site root containing api/deploy.php and index.php; "
        "set ALO_FTPS_ROOT in GitHub Actions variables to the correct folder"
    )


def target_exists(ftp: ftplib.FTP_TLS, name: str) -> bool:
    try:
        ftp.size(name)
        return True
    except ftplib.error_perm as exc:
        if str(exc).startswith(("550", "450")):
            return False
        raise


def transfer(ftp: ftplib.FTP_TLS, root: str, root_path: Path, path: str, commit: str) -> None:
    parts = allowed_relative(path)
    local = root_path / path
    if not local.is_file() or local.is_symlink():
        raise RuntimeError("Local deployment entry is not a regular file")
    if local.stat().st_size > 3 * 1024 * 1024:
        raise RuntimeError("Local deployment entry exceeds 3 MB")
    ftp.cwd(root)
    for segment in parts[:-1]:
        try:
            ftp.cwd(segment)
        except ftplib.error_perm as exc:
            if not str(exc).startswith("550"):
                raise
            ftp.mkd(segment)
            ftp.cwd(segment)
    name = parts[-1]
    # Preserve the executable extension on temporary/backup PHP copies:
    # Apache must never expose raw PHP source through extensionless temp URLs.
    suffix = Path(name).suffix
    tmp = f"{name}.upload-{commit[:10]}-{secrets.token_hex(4)}{suffix}"
    backup = f"{name}.backup-{secrets.token_hex(5)}{suffix}"
    old_moved = False
    tmp_uploaded = False
    try:
        with local.open("rb") as fh:
            ftp.storbinary(f"STOR {tmp}", fh, blocksize=65536)
        tmp_uploaded = True
        ftp.voidcmd("TYPE I")
        remote_size = ftp.size(tmp)
        if remote_size is not None and int(remote_size) != local.stat().st_size:
            raise RuntimeError("Remote FTPS temporary file size mismatch")
        if target_exists(ftp, name):
            ftp.rename(name, backup)
            old_moved = True
        try:
            ftp.rename(tmp, name)
            tmp_uploaded = False
        except Exception:
            if old_moved:
                ftp.rename(backup, name)
                old_moved = False
            raise
        if old_moved:
            ftp.delete(backup)
            old_moved = False
        print("FTPS_DEPLOYED", path, flush=True)
    finally:
        if tmp_uploaded:
            try:
                ftp.delete(tmp)
            except ftplib.all_errors:
                pass
        if old_moved:
            # If the post-install backup cleanup failed, leave the backup
            # rather than replacing an already installed valid new file.
            print("FTPS_BACKUP_LEFT", path, flush=True)


def deploy_ftps(paths: list[str], commit: str, root_path: Path) -> None:
    host = require("ALO_FTPS_HOST")
    username = require("ALO_FTPS_USERNAME")
    password = require("ALO_FTPS_PASSWORD")
    if not re.fullmatch(r"[A-Za-z0-9.-]{3,253}", host) or ".." in host:
        raise RuntimeError("Invalid ALO_FTPS_HOST; set a hostname, not a URL")
    port = int(os.getenv("ALO_FTPS_PORT", "21"))
    if not 1 <= port <= 65535:
        raise RuntimeError("Invalid ALO_FTPS_PORT")
    context = ssl.create_default_context()
    ftp = ftplib.FTP_TLS(context=context, timeout=35)
    try:
        ftp.connect(host, port)
        ftp.login(username, password)
        ftp.prot_p()
        ftp.set_pasv(True)
        ftp_root = locate_root(ftp)
        print("FTPS_SITE_VERIFIED; uploading", len(paths), "allowlisted files", flush=True)
        for path in paths:
            transfer(ftp, ftp_root, root_path, path, commit)
        print("FTPS_DEPLOY_VERIFIED_OK", len(paths), flush=True)
    finally:
        try:
            ftp.quit()
        except Exception:
            ftp.close()
