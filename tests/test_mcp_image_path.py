#!/usr/bin/env python3
"""Regression tests for safe local editorial image uploads."""
import ast
import pathlib
import unittest

ROOT = pathlib.Path(__file__).resolve().parents[1]
SOURCE = ROOT / "scripts" / "mcp_content_job.py"

class ImagePathTests(unittest.TestCase):
    def test_image_path_is_supported_and_conflicts_are_rejected(self):
        tree = ast.parse(SOURCE.read_text(encoding="utf-8"))
        names = {node.name for node in tree.body if isinstance(node, ast.FunctionDef)}
        self.assertIn("load_local_image", names)
        self.assertIn("media_args", names)

    def test_image_path_only_reads_safe_repo_assets(self):
        source = SOURCE.read_text(encoding="utf-8")
        self.assertIn('editorial-images', source)
        self.assertIn('is_relative_to', source)
        self.assertIn('IMAGE_UPDATED_VERIFIED', source)

class ImageOnlyUpdateTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        import importlib.util
        import os
        os.environ.setdefault("MCP_API_TOKEN", "0" * 64)
        spec = importlib.util.spec_from_file_location("alo_mcp_image_only_test", SOURCE)
        cls.client = importlib.util.module_from_spec(spec)
        spec.loader.exec_module(cls.client)

    def test_image_only_update_preserves_existing_brand_article_title(self):
        from unittest.mock import patch
        title = "تعمیر یخچال کنوود در ایزدشهر"
        with patch.object(self.client, "tool", return_value={"record": {"title": title}}) as call:
            result = self.client.preserve_fields_for_image_update(
                "brand_article", 534, {}, {"image_base64": "mock-image"}
            )
        self.assertEqual(result, {"title": title})
        call.assert_called_once_with("get_content", {"type": "brand_article", "id": 534})

    def test_image_only_update_does_not_override_user_supplied_fields(self):
        from unittest.mock import patch
        given = {"des": "existing summary"}
        with patch.object(self.client, "tool") as call:
            result = self.client.preserve_fields_for_image_update(
                "brand_article", 533, given, {"image_base64": "mock-image"}
            )
        self.assertEqual(result, given)
        call.assert_not_called()

    def test_image_only_update_rejects_missing_identity(self):
        from unittest.mock import patch
        with patch.object(self.client, "tool", return_value={"record": {"title": ""}}):
            with self.assertRaises(RuntimeError):
                self.client.preserve_fields_for_image_update(
                    "brand_article", 532, {}, {"image_base64": "mock-image"}
                )


if __name__ == "__main__":
    unittest.main()
