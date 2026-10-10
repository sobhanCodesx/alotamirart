#!/usr/bin/env python3
"""Shared release allowlist for AloTamiratchi: GitHub builds, PHP receives.

No shell, FTP, SSH or Composer on the production cPanel host.
Keep this policy in sync with pnSafe() in api/deployment-agent/index.php.
"""
from __future__ import annotations

import argparse
import hashlib
import json
from pathlib import Path
import re
import subprocess
import zipfile

ROOT = Path(__file__).resolve().parents[1]
APP_ID = "alotamirart-production-v1"
PROTOCOL = 2
ROOT_FILES = frozenset({
    ".htaccess", "index.php", "404.php", "robots.txt",
    "city-services.php", "service-city.php", "service-refrigerator.php",
    "service-washing-machine.php", "show-city.php",
})
SOURCE_DIRS = frozenset({
    "api", "app", "bootstrap", "classes", "config", "database",
    "public", "reqires", "router", "routes", "them", "mapbrand", "mappost",
})
DENIED_SEGMENTS = frozenset({
    ".git", ".github", ".idea", ".env", "cache", "logs", "log", "storage",
    "vendor", "node_modules", "upload", "uploads", "backup", "backups",
    "secrets", "secret", "tmp", "temp",
})
EXTENSIONS = frozenset({
    ".php", ".css", ".js", ".json", ".html", ".htm", ".txt", ".svg",
    ".png", ".jpg", ".jpeg", ".webp", ".ico", ".woff", ".woff2",
    ".ttf", ".eot", ".gif", ".xml", ".webmanifest",
})
# These images are mutable production media. The initial bootstrap and every
# subsequent deployment must leave them untouched.
MEDIA_PREFIXES = ("them/admin/dist/img/",)
MAX_SOURCE_BYTES = 3 * 1024 * 1024


def allowed(path: str) -> bool:
    if not isinstance(path, str) or not re.fullmatch(r"[A-Za-z0-9_./-]{1,240}", path):
        return False
    if path in ROOT_FILES:
        return True
    parts = path.split("/")
    if len(parts) < 2 or parts[0] not in SOURCE_DIRS:
        return False
    if any(not p or p in {".", ".."} or p.startswith(".") or
           p.lower() in DENIED_SEGMENTS for p in parts):
        return False
    if path.startswith(MEDIA_PREFIXES):
        return False
    if parts[-1].lower() == "error_log":
        return False
    return Path(path).suffix.lower() in EXTENSIONS


def git(*args: str, root: Path = ROOT) -> str:
    return subprocess.check_output(["git", *args], cwd=root).decode("utf-8").strip()


def _safe_source(root: Path, path: str) -> Path:
    if not allowed(path):
        raise ValueError("Disallowed release path: " + path)
    target = root / path
    if target.is_symlink() or not target.is_file():
        raise ValueError("Not a regular source file: " + path)
    if target.stat().st_size > MAX_SOURCE_BYTES:
        raise ValueError("Source file exceeds 3 MiB: " + path)
    return target


def changed_files(before: str, after: str, root: Path = ROOT) -> tuple[list[str], list[str]]:
    for sha in (before, after):
        if not re.fullmatch(r"[a-f0-9]{40}", sha):
            raise ValueError("Invalid Git commit identifier.")
        git("cat-file", "-e", sha + "^{commit}", root=root)
    # No rename inference; Git records a rename as an explicit delete and add.
    raw = subprocess.check_output(
        ["git", "diff", "--no-renames", "--name-status", "-z", before, after],
        cwd=root,
    ).decode("utf-8")
    fields = raw.split("\0")
    files: set[str] = set()
    deleted: set[str] = set()
    for i in range(0, len(fields) - 1, 2):
        status, path = fields[i], fields[i + 1]
        if not path or not allowed(path):
            continue
        if status == "D":
            deleted.add(path)
        elif status in {"A", "M", "T"}:
            _safe_source(root, path)
            files.add(path)
        else:
            raise ValueError("Unexpected Git change status: " + status)
    if len(files) + len(deleted) > 400:
        raise ValueError("Deployment changes exceed 400 paths.")
    return sorted(files), sorted(deleted)


def manifest(commit: str, base: str, files: list[str], deleted: list[str],
             root: Path = ROOT) -> dict:
    if set(files) & set(deleted):
        raise ValueError("A path cannot be both written and removed.")
    return {
        "app_id": APP_ID,
        "protocol_version": PROTOCOL,
        "git_commit": commit,
        "base_commit": base,
        "source_ref": "refs/heads/main",
        "files": {path: hashlib.sha256(_safe_source(root, path).read_bytes()).hexdigest()
                  for path in files},
        "deleted": deleted,
    }


def bootstrap_files(root: Path = ROOT) -> list[str]:
    tracked = subprocess.check_output(["git", "ls-files", "-z"], cwd=root).decode("utf-8")
    selected: list[str] = []
    for path in tracked.split("\0"):
        if not allowed(path):
            continue
        target = root / path
        # Historical copies of oversized images are already on production.
        # Mutable production images are categorically excluded above.
        if target.is_file() and not target.is_symlink() and target.stat().st_size <= MAX_SOURCE_BYTES:
            selected.append(path)
    if "api/deployment-agent/index.php" not in selected or "api/mcp.php" not in selected:
        raise ValueError("Required MCP or deployment receiver missing from bootstrap.")
    if ".htaccess" not in selected:
        raise ValueError("Required Apache routing file is missing.")
    return sorted(selected)


def build_bootstrap(output: Path, root: Path = ROOT) -> tuple[int, int]:
    paths = bootstrap_files(root)
    output.parent.mkdir(parents=True, exist_ok=True)
    with zipfile.ZipFile(output, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=6) as z:
        for path in paths:
            z.write(root / path, path)
        if (root / ".env.example").is_file():
            z.write(root / ".env.example", ".env.example")
        z.writestr("BOOTSTRAP-README.txt",
                   "AloTamiratchi one-time cPanel bootstrap.\n"
                   "Extract the CONTENTS of this ZIP into the existing website root, "
                   "beside index.php. Do not delete existing site media.\n"
                   "The package intentionally excludes .env, secrets, logs, "
                   "runtime uploads and historical admin images.\n"
                   "Configure the private .env from .env.example before using MCP "
                   "or automatic deployment; never commit tokens.\n"
                   "No FTP, SSH, CMD, shell or Composer is needed on hosting.\n")
    return len(paths), output.stat().st_size


if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("mode", choices=["bootstrap"])
    parser.add_argument("--output", required=True)
    args = parser.parse_args()
    count, size = build_bootstrap(Path(args.output))
    print(f"ALO_BOOTSTRAP_READY files={count} bytes={size} path={args.output}")
