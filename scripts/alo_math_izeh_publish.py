#!/usr/bin/env python3
"""One user-authorized article in AloTamiratchi, with 3 original optimized WebPs.
Resumable by exact slug, not by creating duplicates; never touches site code."""
import base64,hashlib,json,re,sys,time,urllib.request
from pathlib import Path
sys.path.insert(0,"scripts")
from mcp_content_job import tool,create_content_with_optional_field_fallback
from alo_math_izeh_images import build
P=Path("editorial/math-private-tutoring-izeh-1405.json")
spec=json.loads(P.read_text(encoding="utf-8"))
SLUG="private-math-tutoring-izeh-1405"
TITLE="کلاس خصوصی ریاضی در ایذه | تدریس مفهومی، رفع اشکال و آمادگی امتحان"
assert spec["slug"]==SLUG and spec["title"]==TITLE and spec["post_id"]==10
template=spec["content"]
assert template.count("[[INLINE_1]]")==1 and template.count("[[INLINE_2]]")==1
assert template.count('href="tel:09168949864"')==3
assert len(template.split())>=1250
assert not re.search(r'<script|<iframe',template,re.I)
assert len(re.findall(r'<h2>',template))>=9
assert all(re.fullmatch(r"09[0-9]{9}",spec[k]) for k in ("contact_number",))

def current():
    return tool("find_content",{"type":"article","slug":SLUG})["record"]
def read(a):
    return tool("get_content",{"type":"article","id":int(a["id"])})["record"]
def verify_path(path):
    if not isinstance(path,str) or not re.fullmatch(r"them/admin/dist/img/img-[0-9a-f]{32}\.webp",path):
        raise RuntimeError("Managed image path unexpected")
    return path

cats=tool("list_content",{"type":"category","limit":50})["records"]
assert any(int(x["id"])==10 and x["title"]=="سایر" for x in cats)
visuals=build()
assert len(visuals)==3 and all(24<=len(x)<=30720 and x[:4]==b"RIFF" and x[8:12]==b"WEBP" for x in visuals)
b64=[base64.b64encode(x).decode("ascii") for x in visuals]
print("THREE_DISTINCT_WEBPS",[(len(x),hashlib.sha256(x).hexdigest()[:12]) for x in visuals])
assert len(set(hashlib.sha256(x).digest() for x in visuals))==3
a=current()
if a is None:
    fields={k:spec[k] for k in ("title","slug","content","description","keyword","tags","contact_number","post_id")}
    try:
        a=create_content_with_optional_field_fallback("article",fields,{"image_base64":b64[0]})["record"]
    except Exception:
        # Never automatically retry uncertain CREATE outcome.
        a=current()
        if a is None:raise
    print("DRAFT_CREATED_OR_RECOVERED",int(a["id"]))
a=read(a)
assert a["slug"]==SLUG and a["title"]==TITLE and int(a["post_id"])==10
if int(a.get("status") or 0)==1:
    if a["content"].count("<img ")!=2 or "09168949864" not in a["content"]:
        raise RuntimeError("Existing published article differs; refusing to overwrite")
    print("ALREADY_PUBLISHED",a["id"])
else:
    original=a["content"]
    if original.count("[[INLINE_1]]")==1:
        assert original==template, "Unexpected draft; stop before edit"
        first=verify_path(a["img"])
        fig='<figure><img loading="lazy" src="/'+first+'" alt="نمایش تمرین‌های حل معادله و هندسه در کلاس خصوصی ریاضی ایذه"><figcaption>حل مفهومی جبر و هندسه با تمرین گام‌به‌گام</figcaption></figure>'
        updated=original.replace("[[INLINE_1]]",fig)
        a=tool("update_content",{"type":"article","id":int(a["id"]),"fields":{"content":updated},"image_base64":b64[1]})["record"]
        print("FIRST_INLINE_INSERTED_SECOND_MEDIA_UPLOADED",int(a["id"]))
        a=read(a)
    if a["content"].count("[[INLINE_2]]")==1:
        assert a["content"].count("<img ")==1 and "[[INLINE_1]]" not in a["content"]
        second=verify_path(a["img"])
        fig='<figure><img loading="lazy" src="/'+second+'" alt="مراحل ارزیابی، تدریس مفهومی، تمرین و سنجش در آموزش خصوصی ریاضی"><figcaption>چهار مرحله کاربردی برای سنجش پیشرفت دانش‌آموز</figcaption></figure>'
        final=a["content"].replace("[[INLINE_2]]",fig)
        a=tool("update_content",{"type":"article","id":int(a["id"]),"fields":{"content":final},"image_base64":b64[2]})["record"]
        print("SECOND_INLINE_INSERTED_FEATURED_IMAGE_UPLOADED",int(a["id"]))
        a=read(a)
    assert "[[INLINE_" not in a["content"],"Unexpected media insertion state"
    assert a["content"].count("<img ")==2
    assert a["content"].count('href="tel:09168949864"')==3
    cover=verify_path(a["img"])
    inline=re.findall(r'src="/(them/admin/dist/img/img-[0-9a-f]{32}\.webp)"',a["content"])
    assert len(inline)==2 and len(set(inline+[cover]))==3
    # Verify images are already public BEFORE publishing the draft.
    for path in [cover]+inline:
        url="https://alotamiratchi.ir/"+path
        request=urllib.request.Request(url,headers={"User-Agent":"Mozilla/5.0"})
        with urllib.request.urlopen(request,timeout=30) as resp:
            blob=resp.read(35000)
            assert resp.status==200 and 24<=len(blob)<=30720 and blob[:4]==b"RIFF"
        print("PUBLIC_IMAGE_VERIFIED",len(blob),path)
    published=tool("set_content_published",{"type":"article","id":int(a["id"]),"published":True,"confirm":True})["record"]
    assert int(published["status"])==1
    a=read(published)

assert int(a["status"])==1 and a["slug"]==SLUG
assert a["content"].count("<img ")==2 and a["content"].count('href="tel:09168949864"')==3
assert a.get("img") and a["content"].count("[[INLINE_")==0
url="https://alotamiratchi.ir/post/"+str(a["id"])+"/"+SLUG
page=None
for attempt in range(3):
    try:
        request=urllib.request.Request(url,headers={"User-Agent":"Mozilla/5.0"})
        with urllib.request.urlopen(request,timeout=30) as resp:
            page=resp.read(500000).decode("utf-8","replace")
            assert resp.status==200
        if "09168949864" in page and TITLE in page:break
    except Exception:
        pass
    time.sleep(2)
if not page or "09168949864" not in page or TITLE not in page:
    raise RuntimeError("API confirms published but anonymous page not verified; do not create another article")
print("PUBLISHED_LIVE_VERIFIED",url,"ID",a["id"],"FEATURED_PLUS_INLINE",3,"PHONE",spec["contact_number"])
