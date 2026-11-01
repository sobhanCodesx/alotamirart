#!/usr/bin/env python3
"""Read-only live QA of all 33 Amol refrigerator brand articles. No mutations."""
import collections,hashlib,html,json,re,sys,time
sys.path.insert(0,"scripts")
from mcp_content_job import tool
ARTICLES=[
(257,"سامسونگ","samsung"),(254,"ال جی","lg"),(490,"بوش","bosch"),(242,"دوو","daewoo"),(260,"اسنوا","snowa"),
(245,"امرسان","emersun"),(244,"الکترواستیل","electrosteel"),(243,"دیپوینت","depoint"),(492,"هیوندا","hyundai"),
(256,"فیلور","filver"),(241,"کلور","clever"),(251,"هیتاچی","hitachi"),(493,"هایسنس","hisense"),
(264,"مدیا","midea"),(253,"کنوود","kenwood"),(240,"بکو","beko"),(250,"هیمالیا","himalaya"),
(255,"پارس","pars"),(495,"یخساران","yakhsaran"),(491,"ایستکول","eastcool"),(502,"برفاب","barfab"),
(498,"سینجر","singer"),(265,"وستینگ‌هاوس","westinghouse"),(246,"فریجیدر","frigidaire"),
(263,"ویرپول","whirlpool"),(247,"جنرال الکتریک","general-electric"),(262,"وستل","vestel"),
(499,"اورست","everest"),(500,"التو","alto"),(503,"نیکسان","nixan"),(505,"ایران شرق","iran-shargh"),
(504,"سینور","sinor"),(501,"سیلور","silver")]
para_fingerprints=collections.defaultdict(list);summaries=[]
for article_id,brand,slug in ARTICLES:
 try:
  a=tool("get_content",{"type":"brand_article","id":article_id})["record"]
  content=str(a.get("content") or "")
  plain=html.unescape(re.sub(r"<[^>]+>"," ",content))
  words=len(plain.split())
  links=re.findall(r'href=["\']([^"\']+)["\']',content,re.I)
  internal=[u for u in links if u.startswith(("https://alotamiratchi.ir/","/"))]
  external=[u for u in links if u.startswith(("https://tekanino.ir/","https://khadamatrad.ir/"))]
  link_tek=[u for u in external if "/refrigerator/amol/" in u]
  paragraphs=re.findall(r"<p\b[^>]*>(.*?)</p>",content,re.I|re.S)
  for p in paragraphs:
   normalized=" ".join(html.unescape(re.sub(r"<[^>]*>"," ",p)).split())
   if len(normalized.split())>=35:para_fingerprints[hashlib.sha256(normalized.encode()).hexdigest()].append((article_id,brand))
  x={"id":article_id,"brand":brand,"slug":a.get("slug"),"title":a.get("title"),"status":int(a.get("status") or 0),"words":words,"cover":bool(a.get("img")),"inline":len(re.findall(r"<img\b",content,re.I)),"h2":len(re.findall(r"<h2\b",content,re.I)),"internal":len(internal),"external":len(external),"tek_amol":len(link_tek),"meta":bool(a.get("description")),"links":links[:9]}
  issues=[]
  if x["status"]!=1:issues.append("not_published")
  if words<1000:issues.append("short")
  if not x["cover"]:issues.append("no_cover")
  if x["inline"]<1:issues.append("no_inline")
  if x["internal"]<1:issues.append("no_internal")
  if x["external"]<1:issues.append("no_external")
  if not x["tek_amol"]:issues.append("no_tek_amol")
  if x["h2"]<4:issues.append("weak_structure")
  if not x["meta"]:issues.append("no_meta")
  x["issues"]=issues;summaries.append(x)
  print("AMOL_33_QA",json.dumps(x,ensure_ascii=False),flush=True)
 except Exception as exc:
  print("AMOL_33_QA_ERROR",article_id,brand,str(exc)[:220],flush=True)
 time.sleep(.2)
duplicates={k:v for k,v in para_fingerprints.items() if len(set(x[0] for x in v))>1}
print("AMOL_33_DUPLICATE_PARAGRAPHS",json.dumps(list(duplicates.values())[:30],ensure_ascii=False),flush=True)
counter=collections.Counter(v for x in summaries for v in x["issues"])
print("AMOL_33_AUDIT_SUMMARY",json.dumps({"processed":len(summaries),"expected":len(ARTICLES),"issue_counts":counter,"under_1000":[{"id":x["id"],"brand":x["brand"],"words":x["words"]} for x in summaries if x["words"]<1000],"no_links":[{"id":x["id"],"brand":x["brand"],"issues":x["issues"]} for x in summaries if any("external" in t or "internal" in t for t in x["issues"])],"duplicate_groups":len(duplicates)},ensure_ascii=False),flush=True)
if len(summaries)!=len(ARTICLES):raise RuntimeError("Incomplete live quality audit")
