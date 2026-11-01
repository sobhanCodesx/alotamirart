#!/usr/bin/env python3
"""Read-only final QA of Amol refrigerator brand content. No website changes."""
import html,re,sys,time,hashlib,collections,json
sys.path.insert(0,"scripts")
from mcp_content_job import tool
expected=[
(491,"ایستکول","eastcool"),(492,"هیوندا","hyundai"),(493,"هایسنس","hisense"),
(495,"یخساران","yakhsaran"),(498,"سینجر","singer"),(499,"اورست","everest"),
(500,"التو","alto"),(501,"سیلور","silver"),(502,"برفاب","barfab"),
(503,"نیکسان","nixan"),(504,"سینور","sinor"),(505,"ایران شرق","iran-shargh"),
]
res=[]
norm_paragraphs=collections.defaultdict(list)
for article_id,brand,slug in expected:
    record=tool("get_content",{"type":"brand_article","id":article_id})["record"]
    title=str(record.get("title",""))
    content=str(record.get("content",""))
    plain=html.unescape(re.sub(r"<[^>]+>"," ",content))
    words=len(plain.split())
    links=re.findall(r'href="([^"]+)"',content,re.I)
    internal=[x for x in links if x.startswith(("https://alotamiratchi.ir/","/"))]
    external=[x for x in links if x.startswith(("https://tekanino.ir/","https://khadamatrad.ir/"))]
    tek=[x for x in external if "/refrigerator/amol/" in x]
    is_live=int(record.get("status") or 0)==1
    has_cover=bool(record.get("img"))
    info={"id":article_id,"brand":brand,"title":title,"slug":record.get("slug"),
          "url":"https://alotamiratchi.ir/"+str(record.get("slug"))+"/"+str(article_id),
          "words":words,"internal":len(internal),"external":len(external),"tek_amol":len(tek),
          "inline":len(re.findall(r"<img\b",content,re.I)),"cover":has_cover,"live":is_live}
    print("AMOL_FINAL_QA",json.dumps(info,ensure_ascii=False),flush=True)
    assert is_live and has_cover and words>=1000 and brand in title and "آمل" in title, "Bad article: "+str(article_id)
    assert len(internal)>0 and len(external)>0 and len(tek)>0,"Missing contextual links "+str(article_id)
    assert len(re.findall(r"<h2\b",content,re.I))>=5,"Missing structure "+str(article_id)
    for p in re.findall(r"<p[^>]*>(.*?)</p>",content,re.I|re.S):
        normalized=" ".join(html.unescape(re.sub(r"<[^>]*>"," ",p)).split())
        if len(normalized.split())>=55:
            norm_paragraphs[hashlib.sha256(normalized.encode()).hexdigest()].append(article_id)
    res.append(info)
    time.sleep(.15)
duplicated={k:v for k,v in norm_paragraphs.items() if len(set(v))>1}
print("AMOL_DISTINCT_PARAGRAPHS_QA",len(duplicated),"duplicates",flush=True)
assert not duplicated, "Full paragraphs repeated across brands"
for duplicate_id in [494,496,497]:
    r=tool("get_content",{"type":"brand_article","id":duplicate_id})["record"]
    status=int(r.get("status") or 0)
    print("AMOL_DUPLICATE_UNPUBLISHED_QA",duplicate_id,"status",status,flush=True)
    assert status!=1,"Duplicate article remains published: "+str(duplicate_id)
print("AMOL_FINAL_QA_COMPLETE",len(res),"published","min_words",min(x["words"] for x in res),
    "min_internal",min(x["internal"] for x in res),"min_external",min(x["external"] for x in res),
    "all_images",all(x["cover"] for x in res),flush=True)
