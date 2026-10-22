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

    def test_text_only_update_does_not_require_featured_image(self):
        import json
        import tempfile
        with tempfile.TemporaryDirectory() as tmp:
            job = Path(tmp) / "update.json"
            job.write_text(json.dumps({
                "type": "article", "action": "update", "id": 510,
                "fields": {"content": "<p>Related article link</p>"}
            }), encoding="utf-8")
            def fake_tool(name, args):
                self.assertEqual(name, "update_content")
                self.assertEqual(args["id"], 510)
                self.assertEqual(args["fields"]["content"], "<p>Related article link</p>")
                self.assertNotIn("image_base64", args)
                self.assertNotIn("image_url", args)
                return {"record": {"id": 510}}
            with patch.object(writer, "media_args", side_effect=AssertionError("Unnecessary image reupload")):
                with patch.object(writer, "tool", side_effect=fake_tool) as mocked:
                    writer.manage_content(job)
            self.assertEqual(mocked.call_count, 1)

    def test_related_links_append_keeps_live_content_and_image(self):
        import json
        import tempfile
        with tempfile.TemporaryDirectory() as tmp:
            path = Path(tmp) / "linking.json"
            path.write_text(json.dumps({
                "type": "article", "action": "append_related_links",
                "id": 510, "expected_slug": "washer-drawer-test",
                "section_html": '<h2>مطالب مرتبط با جاپودری</h2><p><a href="https://khadamatrad.ir/article/test">مطالعه</a></p>'
            }), encoding="utf-8")
            original = {
                "id": 510, "slug": "washer-drawer-test", "status": 1,
                "img": "them/admin/dist/img/old.webp",
                "content": "<p>متن قبلی</p><h2>جمع‌بندی</h2><p>پایان</p>",
            }
            updated = {}
            def fake_tool(name, args):
                if name == "get_content":
                    return {"record": dict(original, **updated)}
                self.assertEqual(name, "update_content")
                self.assertEqual(args["id"], 510)
                self.assertEqual(args["fields"].keys(), {"content"})
                self.assertEqual(args["confirm_public"], True)
                self.assertNotIn("image_base64", args)
                self.assertIn("متن قبلی", args["fields"]["content"])
                self.assertIn("مطالب مرتبط با جاپودری", args["fields"]["content"])
                updated["content"] = args["fields"]["content"]
                return {"record": dict(original, **updated)}
            with patch.object(writer, "tool", side_effect=fake_tool) as mocked:
                writer.manage_content(path)
            self.assertEqual(mocked.call_count, 3)
            self.assertEqual(updated["content"].count("مطالب مرتبط با جاپودری"), 1)

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
