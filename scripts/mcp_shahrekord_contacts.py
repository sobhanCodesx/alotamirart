#!/usr/bin/env python3
"""Update custom contact number only on Shahrekord editorial service and brand pages.

Read live matching records before modifying; refuse truncated searches; verify every write.
"""
import re
CITY="شهرکرد"
PHONE="09139858891"
TYPES=(("article","articles"),("brand_article","brand_articles"))
def update_shahrekord(tool,job):
    if job.get("action")!="bulk_shahrekord_contacts" or job.get("city")!=CITY or job.get("phone")!=PHONE or job.get("confirm") is not True:
        raise ValueError("Shahrekord scope, number and confirmation must match")
    plan=[]
    for kind,key in TYPES:
        data=tool("query_alo_graph",{"type":kind,"query":CITY,"limit":25,"include_drafts":True})
        rows=data["entities"][key]
        if not isinstance(rows,list) or len(rows)>=25:
            raise RuntimeError("Incomplete Shahrekord "+kind+" listing; no changes made")
        for row in rows:
            identity=int(row["id"])
            current=tool("get_content",{"type":kind,"id":identity})["record"]
            if int(current["id"])!=identity or CITY not in current.get("title",""):
                raise RuntimeError("Mismatch in target city title")
            if "contact_number" not in current:raise RuntimeError("Contact field is not available")
            plan.append((kind,identity,str(current["contact_number"] or "").strip()))
    if not plan:raise RuntimeError("No Shahrekord city-title content found")
    results=[]
    for kind,identity,prior in plan:
        if prior==PHONE:
            results.append((kind,identity,"already"));continue
        updated=tool("update_content",{"type":kind,"id":identity,"fields":{"contact_number":PHONE},"confirm_public":True})["record"]
        if str(updated.get("contact_number") or "").strip()!=PHONE:raise RuntimeError("Write not verified")
        verify=tool("get_content",{"type":kind,"id":identity})["record"]
        if str(verify.get("contact_number") or "").strip()!=PHONE or CITY not in verify.get("title",""):
            raise RuntimeError("Readback not verified")
        results.append((kind,identity,"updated"))
    print("SHAHREKORD_CONTACTS_VERIFIED", "matched",len(results),"updated",sum(z=="updated" for _,_,z in results),"already",sum(z=="already" for _,_,z in results))
    print("SHAHREKORD_MATCHED_IDS",[(kind,i) for kind,i,_ in results])
    return results
