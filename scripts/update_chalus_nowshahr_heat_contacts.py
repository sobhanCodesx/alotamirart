#!/usr/bin/env python3
"""Apply contact for Chalus/Nowshahr package and gas stove content via authenticated MCP."""
import json, sys, time
sys.path.insert(0, "scripts")
from mcp_content_job import tool

args={"phone":"09901068051","confirm":False,"dry_run":True}
for attempt in range(12):
    try:
        preview=tool("set_two_city_heat_contact",args)
        break
    except Exception as exc:
        print("MCP_SCOPED_TOOL_NOT_READY",attempt+1,type(exc).__name__,str(exc)[:220],flush=True)
        if attempt==11: raise
        time.sleep(25)
assert preview["dry_run"] is True and preview["phone"]=="09901068051"
assert sum(preview["matched"].values())>0
print("PREVIEW",json.dumps(preview,ensure_ascii=False),flush=True)
done=tool("set_two_city_heat_contact",{"phone":"09901068051","confirm":True,"dry_run":False})
assert done["transaction_completed"] is True and done["matched"]==preview["matched"]
print("UPDATED_AND_VERIFIED",json.dumps(done,ensure_ascii=False),flush=True)
