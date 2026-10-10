#!/usr/bin/env python3
import copy
import sys
import unittest
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "scripts"))
from mcp_mahshahr_contacts import apply_mahshahr_contacts


class InMemoryMcp:
    def __init__(self):
        self.rows = {
            ("article", 1): {"id": 1, "title": "تعمیرات یخچال در بندر ماهشهر", "content": "untouched", "status": 1, "contact_number": None},
            ("article", 2): {"id": 2, "title": "تعمیر تلویزیون ماهشهر", "content": "untouched", "status": 0, "contact_number": "09111111111"},
            ("brand_article", 4): {"id": 4, "title": "نمایندگی گری بندرماهشهر", "content": "untouched", "status": 1, "contact_number": ""},
        }
        self.writes = []
    def call(self, name, args):
        if name == "query_alo_graph":
            typ=args["type"]
            assert args["query"] == "ماهشهر" and args["include_drafts"] is True
            key = "articles" if typ == "article" else "brand_articles"
            return {"entities": {key: [copy.deepcopy(row) for (t, _), row in self.rows.items() if t == typ and "ماهشهر" in row["title"]]}}
        row = self.rows[(args["type"], args["id"])]
        if name == "get_content":
            return {"record": copy.deepcopy(row)}
        if name == "update_content":
            assert args["confirm_public"] is True
            assert args["fields"] == {"contact_number": "09169522521"}
            row.update(args["fields"])
            self.writes.append((args["type"], args["id"]))
            return {"record": copy.deepcopy(row)}
        raise AssertionError(name)


class MahshahrBulkContactTests(unittest.TestCase):
    def test_all_post_types_and_drafts_updated_only_contact_field(self):
        mcp=InMemoryMcp()
        before={k:copy.deepcopy(v) for k,v in mcp.rows.items()}
        result=apply_mahshahr_contacts(mcp.call, {
            "action":"bulk_mahshahr_contacts", "city":"ماهشهر",
            "phone":"09169522521", "confirm": True,
        })
        self.assertEqual(result["matched"],3)
        self.assertEqual(len(mcp.writes),3)
        for key,row in mcp.rows.items():
            self.assertEqual(row["contact_number"],"09169522521")
            self.assertEqual(row["content"],before[key]["content"])
            self.assertEqual(row["status"],before[key]["status"])
        result=apply_mahshahr_contacts(mcp.call, {
            "action":"bulk_mahshahr_contacts", "city":"ماهشهر",
            "phone":"09169522521", "confirm": True,
        })
        self.assertEqual(len(result["already_correct"]),3)
        self.assertEqual(len(mcp.writes),3)

    def test_invalid_scope_and_phone_fail_before_writes(self):
        for patch in ({"city":"آمل"},{"phone":"1234"},{"confirm":False}, {"action":"update"}):
            fake=InMemoryMcp()
            request={"action":"bulk_mahshahr_contacts","city":"ماهشهر",
                     "phone":"09169522521","confirm":True}
            request.update(patch)
            with self.assertRaises(ValueError):
                apply_mahshahr_contacts(fake.call,request)
            self.assertFalse(fake.writes)

    def test_incomplete_search_refuses_any_write(self):
        fake=InMemoryMcp()
        original=fake.call
        def overflow(name,args):
            value=original(name,args)
            if name=="query_alo_graph" and args["type"]=="article":
                value["entities"]["articles"]*=13
                value["entities"]["articles"]=value["entities"]["articles"][:25]
            return value
        with self.assertRaisesRegex(RuntimeError,"incomplete"):
            apply_mahshahr_contacts(overflow,{
                "action":"bulk_mahshahr_contacts","city":"ماهشهر",
                "phone":"09169522521","confirm":True,
            })
        self.assertFalse(fake.writes)

if __name__=="__main__":
    unittest.main()
