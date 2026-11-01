#!/usr/bin/env python3
"""Read-only: determine which Amol refrigerator brand guides already exist in Alo."""
import json,os,sys,time,urllib.request,urllib.error
sys.path.insert(0,"scripts")
from mcp_content_job import tool
brands=[
 ("سامسونگ","samsung"),("ال جی","lg"),("بوش","bosch"),("دوو","daewoo"),
 ("اسنوا","snowa"),("امرسان","emersun"),("الکترواستیل","electrosteel"),("دیپوینت","depoint"),
 ("هیوندا","hyundai"),("فیلور","filver"),("کلور","clever"),("هیتاچی","hitachi"),("هایسنس","hisense"),
 ("مدیا","midea"),("کنوود","kenwood"),("بکو","beko"),("هیمالیا","himalaya"),("پارس","pars"),
 ("یخساران","yakhsaran"),("ایستکول","eastcool"),("برفاب","barfab"),("سینجر","singer"),
 ("وستینگ‌هاوس","westinghouse"),("فریجیدر","frigidaire"),("ویرپول","whirlpool"),("جنرال الکتریک","general-electric"),
 ("وستل","vestel"),("اورست","everest"),("التو","alto"),("نیکسان","nixan"),("ایران شرق","iran-shargh"),
 ("سینور","sinor"),("سیلور","silver")]
def query(kind,phrase):
  result=tool("query_alo_graph",{"type":kind,"query":phrase,"limit":25,"include_drafts":False})
  entities=result.get("entities") or {}
  return entities.get("brand_articles" if kind=="brand_article" else "articles",[])
def exists_in_amol(row):
  title=str(row.get("title") or "");slug=str(row.get("slug") or "")
  return ("آمل" in title or "آمل" in str(row.get("city_name") or "") or "-amol" in slug or "amol-" in slug) and ("یخچال" in title or "فریزر" in title or "refrigerator" in slug or "fridge" in slug)
def public_rows(items):
  return [{"id":x.get("id"),"title":x.get("title"),"slug":x.get("slug"),"brand_name":x.get("brand_name")} for x in items]
total=0
broad={}
for kind in ("article","brand_article"):
  broad[kind]=query(kind,"آمل")
  print("AMOL_BROAD",kind,len(broad[kind]),json.dumps(public_rows([x for x in broad[kind] if exists_in_amol(x)]),ensure_ascii=False),flush=True)
seen={}
for name,slug in brands:
  # Check both editorial article and brand-specific article tables. Search brand name first to avoid 25-result Amol cap.
  matches=[]
  for kind in ("article","brand_article"):
    try:
      candidates=query(kind,name)
    except Exception as error:
      print("AMOL_INVENTORY_QUERY_ERROR",name,kind,type(error).__name__,flush=True);raise
    matches.extend([dict(x,source_type=kind) for x in candidates if exists_in_amol(x)])
    time.sleep(.15)
  seen[slug]=public_rows(matches)
  total+=len(matches)
  print("AMOL_BRAND_AUDIT",json.dumps({"brand":name,"slug":slug,"existing":seen[slug]},ensure_ascii=False),flush=True)
print("AMOL_INVENTORY_COMPLETE",json.dumps({"brands":len(brands),"matching_existing":total,"needs_new":[{"name":n,"slug":s} for n,s in brands if not seen[s]]},ensure_ascii=False),flush=True)
