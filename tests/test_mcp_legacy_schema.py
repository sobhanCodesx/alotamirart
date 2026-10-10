#!/usr/bin/env python3
"""Regression tests for live legacy-schema article publishing."""
import os
import sys
import unittest
from pathlib import Path
from unittest.mock import patch
os.environ.setdefault("MCP_API_TOKEN", "CI-ONLY-"+("X"*45))
sys.path.insert(0,str(Path(__file__).resolve().parents[1]/"scripts"))
import mcp_content_job as writer

class LegacySchemaTests(unittest.TestCase):
    def test_optional_fallback_preserves_required_seo_fields(self):
        fields={"title":"Guide","slug":"guide","content":"Useful content","post_id":9,
                "description":"Meta","keyword":"fridge","tags":"cooling"}
        def fake(name,args):
            self.assertEqual(name,"create_content")
            self.assertEqual(args["fields"]["post_id"],9)
            if "keyword" in args["fields"]:
                raise RuntimeError("Unknown/unavailable content field: keyword")
            if "tags" in args["fields"]:
                raise RuntimeError("Unknown/unavailable content field: tags")
            return {"record":{"id":1,"title":"Guide"}}
        with patch.object(writer,"tool",side_effect=fake) as mocked:
            outcome=writer.create_content_with_optional_field_fallback("article",fields,{})
        self.assertEqual(outcome["record"]["id"],1)
        self.assertEqual(mocked.call_count,3)
        self.assertIn("keyword",fields)
        self.assertIn("tags",fields)

    def test_required_and_unexpected_fields_are_never_dropped(self):
        for error in ["Unknown/unavailable content field: title",
                      "Unknown/unavailable content field: description",
                      "Image download failed","Invalid author"]:
            with self.subTest(error=error):
                with patch.object(writer,"tool",side_effect=RuntimeError(error)):
                    with self.assertRaisesRegex(RuntimeError,error):
                        writer.create_content_with_optional_field_fallback(
                            "article",{"title":"Guide","post_id":9},{})

if __name__=="__main__":
    unittest.main()
