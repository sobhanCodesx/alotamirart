#!/usr/bin/env python3
"""Update exactly the user-authorized northern-city service contacts; exclude specified services."""
import json,sys
sys.path.insert(0,"scripts")
from mcp_content_job import tool
PHONE="09351211661"
cities=["آمل","قائمشهر","بابل","بابلسر","سرخرود","ایزدشهر","نور","رویان","متل قو","چالوس","نوشهر","فریدونکنار","محمودآباد"]
preview=tool("set_north_cities_operator_contact",{"phone":PHONE,"confirm":False,"dry_run":True})
assert preview["dry_run"] is True and preview["phone"]==PHONE
assert set(preview["cities"])==set(cities)
assert sum(preview["matched"].values())>0
print("NORTH_CONTACT_PREVIEW",json.dumps({k:preview[k] for k in ("matched","changed_or_would_change","excluded_count")},ensure_ascii=False),flush=True)
result=tool("set_north_cities_operator_contact",{"phone":PHONE,"confirm":True,"dry_run":False})
assert result["transaction_completed"] is True and result["phone"]==PHONE
assert result["matched"]==preview["matched"]
print("NORTH_CONTACT_UPDATED",json.dumps({k:result[k] for k in ("matched","changed_or_would_change","excluded_count")},ensure_ascii=False),flush=True)
