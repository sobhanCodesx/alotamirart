#!/usr/bin/env python3
import unittest
from pathlib import Path
import sys
sys.path.insert(0,str(Path(__file__).resolve().parents[1]/"scripts"))
from mcp_context_job import validate

class ReadQueueTests(unittest.TestCase):
    def test_only_public_bounded_queries(self):
        self.assertEqual(validate({"tool":"query_alo_graph","arguments":{"type":"category","limit":25}})["name"],"query_alo_graph")
        for args in ({"include_drafts":True},{"include_drafts":None},{"limit":1000},{"type":"users"},{"sql":"select * from users"},{"query":"x"*101}):
            with self.subTest(args=args),self.assertRaises(ValueError):
                validate({"tool":"query_alo_graph","arguments":args})
        with self.assertRaises(ValueError):
            validate({"tool":"publish_article","arguments":{"id":1}})
if __name__=="__main__":
    unittest.main()
