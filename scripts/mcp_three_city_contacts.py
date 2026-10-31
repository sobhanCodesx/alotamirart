#!/usr/bin/env python3
"""Update published/draft article contact_number only when the title names one of three cities."""
import re
CITIES=("هشتگرد","نظرآباد","آبیک")
PHONE="09371141762"
TYPES=(("article","articles"),("brand_article","brand_articles"))
def exact_city_title(title, city):
    return re.search(r"(?<![\u0600-\u06ff])"+re.escape(city)+r"(?![\u0600-\u06ff])",title or "") is not None
def update_three_city_contacts(tool,request):
    if request.get("action")!="bulk_three_city_contacts" or request.get("phone")!=PHONE or request.get("cities")!=list(CITIES) or request.get("confirm") is not True:
        raise ValueError("Exact city scope, number and confirmation required")
    plan={}
    stats={}
    for city in CITIES:
        for kind,key in TYPES:
            data=tool("query_alo_graph",{"type":kind,"query":city,"limit":25,"include_drafts":True})
            rows=data["entities"][key]
            if not isinstance(rows,list) or len(rows)>=25:raise RuntimeError("Truncated list for "+city+" "+kind+"; no write")
            for row in rows:
                identity=int(row["id"])
                record=tool("get_content",{"type":kind,"id":identity})["record"]
                if int(record["id"])!=identity or not exact_city_title(record.get("title",""),city):
                    continue
                if "contact_number" not in record:raise RuntimeError("Contact field unavailable")
                if (kind,identity) in plan and plan[(kind,identity)]["city"]!=city:
                    raise RuntimeError("Article title names multiple target cities; refuse ambiguous edit")
                plan[(kind,identity)]={"city":city,"before":str(record.get("contact_number") or "").strip()}
                stats[city]=stats.get(city,0)+1
    if not plan:raise RuntimeError("No matching city article records: check graph connectivity")
    updated=0
    for (kind,id),data in sorted(plan.items()):
        if data["before"]!=PHONE:
            result=tool("update_content",{"type":kind,"id":id,"fields":{"contact_number":PHONE},"confirm_public":True})["record"]
            if str(result.get("contact_number") or "").strip()!=PHONE:raise RuntimeError("Write not verified "+str(id))
            updated+=1
        verify=tool("get_content",{"type":kind,"id":id})["record"]
        if str(verify.get("contact_number") or "").strip()!=PHONE or not exact_city_title(verify.get("title",""),data["city"]):
            raise RuntimeError("Readback or city identity failed "+str(id))
    print("ALO_THREE_CITY_CONTACTS_VERIFIED","matched",len(plan),"changed",updated,"per_city",stats,"ids",list(plan))
    return len(plan)
