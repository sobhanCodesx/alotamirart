#!/usr/bin/env python3
"""Change ONLY featured cover of already published article 508 to the user's selected photo."""
import base64
import hashlib
import json
import re
import sys
import urllib.request
from pathlib import Path

sys.path.insert(0,"scripts")
from mcp_content_job import tool

ID=508
SLUG="private-math-tutoring-izeh-1405"
TITLE="کلاس خصوصی ریاضی در ایذه | تدریس مفهومی، رفع اشکال و آمادگی امتحان"
PAYLOAD=Path("editorial/alo-math-508-cover-20261019.json")
HOST="https://alotamiratchi.ir"
file=json.loads(PAYLOAD.read_text(encoding="utf-8"))
assert file["article_id"]==ID
assert file["expected_slug"]==SLUG
assert file["image_filename"]=="alo_math_508_new_cover_29094.webp"
encoded=file["image_base64"]
assert isinstance(encoded,str) and len(encoded)<=40000
raw=base64.b64decode(encoded,validate=True)
assert 1024<len(raw)<=30000 and len(raw)==file["image_bytes"]
assert raw[:4]==b"RIFF" and raw[8:12]==b"WEBP"
assert hashlib.sha256(raw).hexdigest()==file["image_sha256"]
print("TARGET_WEBP_VALIDATED",len(raw),"SHA256",file["image_sha256"])

def download_image(path):
    assert re.fullmatch(r"them/admin/dist/img/img-[0-9a-f]{32}\.webp",path),path
    request=urllib.request.Request(HOST+"/"+path,headers={"User-Agent":"Mozilla/5.0"})
    with urllib.request.urlopen(request,timeout=35) as response:
        assert response.status==200
        return response.read(80000)

def get_article():
    article=tool("get_content",{"type":"article","id":ID})["record"]
    assert article is not None
    assert int(article["id"])==ID
    assert article["slug"]==SLUG and article["title"]==TITLE
    assert int(article["status"])==1
    assert "09168949864" in article["content"]
    assert article["content"].count("<img ")==2
    assert article["img"]
    return article

before=get_article()
prior=before["img"]
print("CURRENT_COVER",prior)

if hashlib.sha256(download_image(prior)).digest()==hashlib.sha256(raw).digest():
    print("ALREADY_SET_CORRECT_COVER")
    after=before
else:
    result=tool("update_content",{
        "type":"article",
        "id":ID,
        "fields":{},
        "image_base64":encoded,
        "confirm_public":True
    })["record"]
    assert int(result["id"])==ID and result["img"]!=prior
    after=get_article()
    assert after["img"]==result["img"]
    for field in ("id","slug","title","content","description","post_id","status","user_id"):
        assert str(after.get(field))==str(before.get(field)),"Modified forbidden article field: "+field
    print("FEATURED_IMAGE_REPLACED",prior,"=>",after["img"])

public_img=download_image(after["img"])
assert len(public_img)<=30000 and hashlib.sha256(public_img).digest()==hashlib.sha256(raw).digest()
print("PUBLIC_FEATURED_WEBP_VERIFIED",len(public_img),"SHA256",hashlib.sha256(public_img).hexdigest())
for s in re.findall(r'src="/(them/admin/dist/img/img-[0-9a-f]{32}\.webp)"',after["content"]):
    assert s!=after["img"],"Cover unexpectedly reuses an inline image"
print("INLINE_IMAGES_UNCHANGED",after["content"].count("<img "))

url=HOST+"/post/"+str(ID)+"/"+SLUG
req=urllib.request.Request(url,headers={"User-Agent":"Mozilla/5.0"})
with urllib.request.urlopen(req,timeout=35) as response:
    page=response.read(600000).decode("utf-8","replace")
    assert response.status==200
assert "09168949864" in page and TITLE in page
if after["img"] not in page:
    # Image file and API are verified; site cache might briefly serve an old HTML version.
    print("PUBLIC_HTML_IMAGE_REFERENCE_NOT_YET_VISIBLE_CACHE_MAY_BE_STALE")
else:
    print("PUBLIC_HTML_REFERENCES_NEW_IMAGE")
print("SUCCESS_FEATURED_COVER_CHANGED_ON_PUBLISHED_ARTICLE",url,"bytes",len(public_img))
