#!/usr/bin/env python3
"""Contract tests for GitHub-to-cPanel releases without touching production."""
import hashlib
from pathlib import Path
import subprocess
import tempfile
import unittest
import sys
import zipfile

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "scripts"))
import alo_release as release


class ReleaseTests(unittest.TestCase):
    def test_allows_root_routes_but_never_secrets_or_runtime_uploads(self):
        good = [
            ".htaccess", "index.php", "404.php", "robots.txt", "config/database.php",
            "api/deployment-agent/index.php", "routes/web.php",
            "mapbrand/sitemap.php", "them/admin/dist/css/style.css",
        ]
        bad = [
            ".env", ".env.example", "info.php", "dev-router.php", "error_log",
            "storage/logs/run.log", "them/admin/dist/img/private.jpg",
            "them/admin/dist/img/brand/production.webp", "api/../.env",
            "config/.secrets.php", ".github/workflows/deploy.yml",
            "tests/anything.php", "public/uploads/user.png",
            "api/foo.php\\bar", "api/foo.php?evil=1",
        ]
        for path in good:
            with self.subTest(path=path):
                self.assertTrue(release.allowed(path))
        for path in bad:
            with self.subTest(path=path):
                self.assertFalse(release.allowed(path))

    def test_signed_manifest_inputs_include_deleted_and_base(self):
        with tempfile.TemporaryDirectory() as temp:
            root = Path(temp)
            (root / "api").mkdir()
            (root / "api/test.php").write_text("<?php echo 1;", encoding="utf-8")
            sha = "a" * 40
            base = "b" * 40
            data = release.manifest(sha, base, ["api/test.php"], [".htaccess"], root)
            self.assertEqual(data["base_commit"], base)
            self.assertEqual(data["deleted"], [".htaccess"])
            self.assertEqual(data["protocol_version"], 2)
            self.assertEqual(data["files"]["api/test.php"],
                             hashlib.sha256(b"<?php echo 1;").hexdigest())
            with self.assertRaises(ValueError):
                release.manifest(sha, base, ["api/test.php"], ["api/test.php"], root)

    def test_diffs_detect_deletion_and_reject_unsafe_paths(self):
        with tempfile.TemporaryDirectory() as temp:
            root = Path(temp)
            def git(*a):
                return subprocess.check_output(["git", *a], cwd=root).decode().strip()
            git("init", "-q")
            git("config", "user.email", "ci@example.test")
            git("config", "user.name", "CI")
            (root / "api").mkdir()
            (root / "api/old.php").write_text("<?php", encoding="utf-8")
            (root / "api/new.php").write_text("<?php", encoding="utf-8")
            (root / ".env").write_text("secret", encoding="utf-8")
            git("add", ".")
            git("commit", "-qm", "initial")
            before = git("rev-parse", "HEAD")
            (root / "api/old.php").unlink()
            (root / "api/new.php").write_text("<?php echo 1;", encoding="utf-8")
            (root / ".env").write_text("new-secret", encoding="utf-8")
            git("add", "-A")
            git("commit", "-qm", "release")
            after = git("rev-parse", "HEAD")
            self.assertEqual(release.changed_files(before, after, root),
                             (["api/new.php"], ["api/old.php"]))

    def test_bootstrap_archive_excludes_mutable_media(self):
        with tempfile.TemporaryDirectory() as temp:
            root = Path(temp)
            def git(*a):
                subprocess.check_call(["git", *a], cwd=root, stdout=subprocess.DEVNULL)
            git("init", "-q")
            for path, value in {
                ".htaccess": "RewriteEngine On",
                "index.php": "<?php",
                "api/mcp.php": "<?php",
                "api/deployment-agent/index.php": "<?php",
                "them/admin/dist/img/production.jpg": "private user media",
                ".env": "production secret",
            }.items():
                p = root / path
                p.parent.mkdir(parents=True, exist_ok=True)
                p.write_text(value)
            git("add", ".")
            output = root / "bootstrap.zip"
            count, size = release.build_bootstrap(output, root)
            self.assertGreater(count, 0)
            self.assertGreater(size, 0)
            with zipfile.ZipFile(output) as z:
                self.assertIn("api/mcp.php", z.namelist())
                self.assertIn(".htaccess", z.namelist())
                self.assertNotIn(".env", z.namelist())
                self.assertNotIn("them/admin/dist/img/production.jpg", z.namelist())


if __name__ == "__main__":
    unittest.main()
