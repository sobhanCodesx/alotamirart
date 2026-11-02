#!/usr/bin/env python3
"""Read-only originality and SEO hygiene measurements for 15 Chamestan brand articles."""
import re,sys,html,json,statistics
from difflib import SequenceMatcher
sys.path.insert(0,"scripts")
from mcp_content_job import tool
rows=[]
for n in range(506,521):
    a=tool("get_content",{"type":"brand_article","id":n})["record"]
    if a.get("status")!=1 and str(a.get("status"))!="1":raise RuntimeError("Unpublished "+str(n))
    raw=a.get("content") or ""
    words=re.findall(r"[\wآ-ی‌]+",re.sub("<[^>]+>"," ",html.unescape(raw)))
    urls=re.findall(r'href="([^"]+)"',raw)
    stripped=re.sub(r"<[^>]+>"," ",raw)
    # Ignore whitespace and brand names when measuring substantive cross-brand overlap.
    stripped=re.sub(r"سامسونگ|ال‌جی|ال جی|بوش|دوو|اسنوا|امرسان|الکترواستیل|دیپوینت|هیمالیا|بکو|هایسنس|هیتاچی|کنوود|ایستکول|پارس|چمستان","برند",stripped)
    stripped=" ".join(stripped.split())
    rows.append({"id":n,"slug":a.get("slug"),"words":len(words),
                 "internal":sum("alotamiratchi.ir" in u for u in urls),
                 "external":sum("tekanino.ir" in u for u in urls),"text":stripped})
pairs=[]
for i,a in enumerate(rows):
    for b in rows[i+1:]:
        pairs.append((round(SequenceMatcher(None,a["text"],b["text"],autojunk=False).ratio(),3),a["id"],b["id"]))
pairs.sort(reverse=True)
print("CHAMESTAN_SEO_QA",json.dumps({"pages":len(rows),"words_min":min(r["words"] for r in rows),
"words_max":max(r["words"] for r in rows),"words_median":statistics.median(r["words"] for r in rows),
"min_internal":min(r["internal"] for r in rows),"min_external":min(r["external"] for r in rows),
"highest_similarities":pairs[:7],"urls":[{"id":r["id"],"slug":r["slug"]} for r in rows]},ensure_ascii=False),flush=True)
