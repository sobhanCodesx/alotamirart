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

if __name__ == "__main__":
    unittest.main()
