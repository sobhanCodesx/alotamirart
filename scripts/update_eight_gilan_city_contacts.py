#!/usr/bin/env python3
import json, sys
sys.path.insert(0, "scripts")
from mcp_content_job import tool
preview=tool("set_eight_gilan_city_contacts", {"phone":"09108731535","dry_run":True,"confirm":False})
print("GILAN_PREVIEW",json.dumps(preview,ensure_ascii=False),flush=True)
assert preview["dry_run"] is True
assert sum(preview["matched"].values())>0
result=tool("set_eight_gilan_city_contacts", {"phone":"09108731535","dry_run":False,"confirm":True})
assert result["transaction_completed"] is True
assert result["matched"]==preview["matched"]
print("GILAN_COMPLETED",json.dumps(result,ensure_ascii=False),flush=True)
