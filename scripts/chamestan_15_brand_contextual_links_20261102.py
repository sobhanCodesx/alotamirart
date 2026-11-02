#!/usr/bin/env python3
"""Disperse contextual links between verified Chamestan brand pages, preserve images/SEO."""
import json,sys
sys.path.insert(0,"scripts")
from mcp_content_job import tool
BRANDS=[
("samsung","سامسونگ"),("lg","ال‌جی"),("bosch","بوش"),("daewoo","دوو"),("snowa","اسنوا"),
("emersun","امرسان"),("electrosteel","الکترواستیل"),("depoint","دیپوینت"),("himalaya","هیمالیا"),
("beko","بکو"),("hisense","هایسنس"),("hitachi","هیتاچی"),("kenwood","کنوود"),("eastcool","ایستکول"),("pars","پارس")]
RELATIONS={
"samsung":("hisense","lg"),"lg":("kenwood","samsung"),"bosch":("hisense","emersun"),
"daewoo":("beko","emersun"),"snowa":("himalaya","depoint"),"emersun":("beko","bosch"),
"electrosteel":("hitachi","kenwood"),"depoint":("hitachi","eastcool"),
"himalaya":("snowa","lg"),"beko":("emersun","bosch"),
"hisense":("samsung","bosch"),"hitachi":("depoint","electrosteel"),
"kenwood":("pars","lg"),"eastcool":("depoint","pars"),"pars":("kenwood","eastcool")}
base="https://alotamiratchi.ir/"
found={}
errors=[]
for key,name in BRANDS:
    slug=key+"-refrigerator-repair-chamestan"
    record=tool("find_content",{"type":"brand_article","slug":slug}).get("record")
    if not record or str(record.get("slug"))!=slug:
        errors.append((key,"MISSING_BRAND_ARTICLE"));continue
    if int(record.get("status") or 0)!=1 or int(record.get("brand_id") or 0)!=5 or not record.get("img"):
        errors.append((key,"NOT_ACTIVE_WITH_BRAND_IMAGE"));continue
    found[key]=(record,name)
if errors:raise RuntimeError("Cannot crosslink unpublished brand articles: "+str(errors))
print("CHAMESTAN_CROSSLINK_INVENTORY_VERIFIED",len(found),flush=True)
for key,(record,name) in found.items():
    try:
        original=record["content"]
        new=original
        for h,related in enumerate(RELATIONS[key]):
            target,othername=found[related]
            url=base+target["slug"]+"/"+str(target["id"])
            if url in new:continue
            anchor=("چطور نشانه اصلی را از مشکل ظاهری جدا کنیم؟" if h==0 else
                    "آزمایش‌های تخصصی و پیشگیری از تعویض بی‌مورد")
            heading="<h2>"+anchor+"</h2>"
            if new.count(heading)!=1:raise ValueError("Editorial section not found: "+anchor)
            pstart=new.index(heading)+len(heading)
            pend=new.find("</p>",pstart)
            if pend<0 or pend-pstart>2500:raise ValueError("Missing matching paragraph")
            addition=(" برای شناخت تفاوت علائم مشابه، <a href=\""+url+"\">راهنمای یخچال "+
                      othername+" در چمستان</a> را نیز ببینید." if h==0 else
                      " در مقایسه با این ایراد، <a href=\""+url+"\">بررسی تخصصی یخچال "+
                      othername+" در چمستان</a> می‌تواند به تفکیک مشکلات کمک کند.")
            new=new[:pend]+addition+new[pend:]
        if new!=original:
            tool("update_content",{"type":"brand_article","id":int(record["id"]),
                 "fields":{"content":new},"confirm_public":True})
        actual=tool("get_content",{"type":"brand_article","id":int(record["id"])})["record"]
        if (actual["content"]!=new or actual.get("img")!=record.get("img") or
            int(actual.get("status") or 0)!=1 or int(actual.get("brand_id") or 0)!=5 or
            actual.get("slug")!=record.get("slug")):raise RuntimeError("Readback failed")
        print("CHAMESTAN_BRAND_CROSSLINKS_VERIFIED",key,record["id"],len(RELATIONS[key]),flush=True)
    except Exception as ex:
        errors.append((key,type(ex).__name__,str(ex)[:250]))
        print("CHAMESTAN_CROSSLINK_ERROR",key,type(ex).__name__,str(ex)[:250],flush=True)
print("CHAMESTAN_CROSSLINK_RESULT",json.dumps({"expected":len(found),"errors":errors},ensure_ascii=False),flush=True)
if errors:raise SystemExit(2)
