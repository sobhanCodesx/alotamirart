#!/usr/bin/env python3
import os,sys,json,time
sys.path.insert(0,"scripts")
from mcp_content_job import tool
variants={
 "bosch":["بوش"],"hyundai":["هیوندا","هیوندای"],"hisense":["هایسنس"],
 "yakhsaran":["یخساران","یخ ساران"],"eastcool":["ایستکول","ایست کول"],
 "barfab":["برفاب"],"singer":["سینجر","سینژر"],
 "everest":["اورست"],"alto":["التو"],"nixan":["نیکسان"],
 "iran-shargh":["ایران شرق","ایران‌شرق"],"sinor":["سینور"],
 "silver":["سیلور"],
}
for slug,names in variants.items():
 found=[]
 for name in names:
  for typ in ("article","brand_article"):
   d=tool("query_alo_graph",{"type":typ,"query":name,"limit":25,"include_drafts":False})
   items=(d.get("entities") or {}).get("articles" if typ=="article" else "brand_articles",[])
   for x in items:
    title=str(x.get("title") or "");slugx=str(x.get("slug") or "")
    if ("آمل" in title or "-amol" in slugx or "amol-" in slugx) and ("یخچال" in title or "فریزر" in title or "refrigerator" in slugx):
     record={"id":x.get("id"),"title":title,"slug":slugx,"type":typ}
     if record not in found:found.append(record)
  time.sleep(.12)
 print("ALIAS_AMOL_BRAND",json.dumps({"slug":slug,"found":found},ensure_ascii=False),flush=True)
print("ALIAS_AMOL_DONE")
