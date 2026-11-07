#!/usr/bin/env python3
import json,sys
sys.path.insert(0,"scripts")
from mcp_content_job import tool
for query in ["پکیج ایران رادیاتور", "پکیج", "چالوس", "نوشهر"]:
    for typ in ["article","brand_article"]:
        r=tool("query_alo_graph",{"type":typ,"query":query,"limit":25,"include_drafts":True})
        print("GRAPH",query,typ,json.dumps(r.get("entities",{}),ensure_ascii=False),flush=True)
