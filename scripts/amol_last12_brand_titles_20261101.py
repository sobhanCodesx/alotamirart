#!/usr/bin/env python3
"""Update EXACTLY the final 12 Amol refrigerator brand guides in place, without creating duplicates."""
import sys,re,html,json,time
sys.path.insert(0,"scripts")
from mcp_content_job import tool

TARGETS=[
    (491,"ایستکول","eastcool"),
    (492,"هیوندا","hyundai"),
    (493,"هایسنس","hisense"),
    (495,"یخساران","yakhsaran"),
    (498,"سینجر","singer"),
    (499,"اورست","everest"),
    (500,"التو","alto"),
    (501,"سیلور","silver"),
    (502,"برفاب","barfab"),
    (503,"نیکسان","nixan"),
    (504,"سینور","sinor"),
    (505,"ایران شرق","iran-shargh"),
]
def check_record(r,article_id,brand,slug,require_title=False):
    if int(r.get("id",0))!=article_id or int(r.get("status") or 0)!=1:
        raise RuntimeError("Unpublished or wrong record "+str(article_id))
    if "آمل" not in str(r.get("title") or "") and not require_title:
        raise RuntimeError("Not an Amol article "+str(article_id))
    content=str(r.get("content") or "")
    if brand not in str(r.get("title") or "") or len(html.unescape(re.sub(r"<[^>]+>"," ",content)).split())<1000:
        raise RuntimeError("Brand identity or word count mismatch "+str(article_id))
    if not r.get("img") or "<img" not in content or not re.search(r'href=["\']https://tekanino\.ir/refrigerator/amol/',content):
        raise RuntimeError("Missing previously verified photo or Amol link "+str(article_id))
    if slug not in str(r.get("slug") or "") and slug not in str(r.get("title") or ""):
        # Some legacy slugs use different brand spelling; brand in title is authoritative.
        pass

updated=[]
for article_id,brand,slug in TARGETS:
    before=tool("get_content",{"type":"brand_article","id":article_id})["record"]
    check_record(before,article_id,brand,slug)
    title="نمایندگی تعمیر یخچال "+brand+" در آمل"
    old_content=before["content"]
    # A service provider must not masquerade as the factory's authorized representative.
    disclosure="<p><strong>درباره عنوان نمایندگی:</strong> این صفحه راهنمای خدمات تعمیر مستقل یخچال "+brand+" در آمل است و ادعای نمایندگی رسمی یا تأیید مستقیم از سوی شرکت سازنده ندارد.</p>"
    new_content=old_content
    if disclosure not in old_content and "نمایندگی رسمی" not in html.unescape(re.sub(r"<[^>]+>"," ",old_content[:650])):
        end=old_content.find("</p>")
        if end<0:raise RuntimeError("Cannot insert honest disclosure safely for "+str(article_id))
        end+=4
        new_content=old_content[:end]+disclosure+old_content[end:]
    fields={}
    if before["title"]!=title:fields["title"]=title
    if new_content!=old_content:fields["content"]=new_content
    # Brand articles use 'des', not 'description'. Preserve existing SEO metadata.
    description=str(before.get("des") or "").strip()
    if not description:
        fields["des"]="نمایندگی تعمیر یخچال "+brand+" در آمل؛ راهنمای عیب‌یابی مشکلات سرمایش، هزینه تعمیر، نگهداری ایمن و انتخاب تعمیرکار مستقل."
    if fields:
        tool("update_content",{"type":"brand_article","id":article_id,"fields":fields,"confirm_public":True})
    actual=tool("get_content",{"type":"brand_article","id":article_id})["record"]
    if (actual.get("title")!=title or actual.get("slug")!=before.get("slug") or
        actual.get("img")!=before.get("img") or actual.get("content")!=new_content or
        int(actual.get("status") or 0)!=1 or not str(actual.get("des") or "").strip()):
        raise RuntimeError("Readback mismatch for "+str(article_id))
    url="https://alotamiratchi.ir/"+str(actual["slug"])+"/"+str(article_id)
    info={"id":article_id,"brand":brand,"title":title,"url":url,"updated_fields":list(fields),
          "existing_meta_preserved":bool(description),"cover_preserved":True}
    updated.append(info)
    print("AMOL_LAST12_UPDATED",json.dumps(info,ensure_ascii=False),flush=True)
    time.sleep(.12)
if len(updated)!=12:raise RuntimeError("Incomplete set")
print("AMOL_LAST12_COMPLETE",len(updated),"published","all_12_titles_accurate","original_cover_and_slugs_preserved",flush=True)
