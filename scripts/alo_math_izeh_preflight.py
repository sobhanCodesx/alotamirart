#!/usr/bin/env python3
"""Read-only preflight: category fit, title collision, publishing connection."""
import sys
sys.path.insert(0,'scripts')
from mcp_content_job import tool
SLUG="private-math-tutoring-izeh-1405"
categories=tool("list_content",{"type":"category","limit":50})["records"]
print("EXISTING_PUBLIC_CATEGORIES",[(int(x["id"]),x.get("title")) for x in categories])
found=tool("find_content",{"type":"article","slug":SLUG})["record"]
print("EXACT_SLUG_EXISTS",bool(found))
if found:print("EXACT_SLUG_ID",int(found["id"]),"STATUS",int(found.get("status") or 0))
articles=tool("list_content",{"type":"article","limit":50})["records"]
for a in articles:
 if any(k in (a.get("title") or "") for k in ["ریاضی","کلاس خصوصی","ایذه"]):
  print("POTENTIAL_SIMILAR",int(a["id"]),a.get("title") if int(a.get("status") or 0)==1 else "[DRAFT]")
print("READ_ONLY_PREFLIGHT_OK")
