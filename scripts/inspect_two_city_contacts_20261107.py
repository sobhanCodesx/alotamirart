#!/usr/bin/env python3
import json,sys
sys.path.insert(0,"scripts")
from mcp_content_job import tool
for query in ["پکیج ایران رادیاتور", "پکیج", "چالوس", "نوشهر"]:
    for typ in ["article","brand_article"]:
        r=tool("query_alo_graph",{"type":typ,"query":query,"limit":25,"include_drafts":True})
        print("GRAPH",query,typ,json.dumps(r.get("entities",{}),ensure_ascii=False),flush=True)

for item_id in [95,96,97,98,99,100,101,102]:
    try:
        rec=tool("get_content",{"type":"brand_article","id":item_id})
        data=rec.get("item",rec.get("record",rec))
        print("EXACT_BRAND",item_id,json.dumps(data,ensure_ascii=False)[:1700],flush=True)
    except Exception as exc:
        print("EXACT_BRAND_ERR",item_id,str(exc)[:200],flush=True)
