#!/usr/bin/env python3
"""Restore unchanged editorial slug after legacy image-only change auto-regenerated it."""
import hashlib,sys,urllib.request
sys.path.insert(0,"scripts")
from mcp_content_job import tool
ID=508
SLUG="private-math-tutoring-izeh-1405"
ACCIDENTAL="کلاس-خصوصی-ریاضی-در-ایذه--تدریس-مفهومی-رفع-اشکال-و-آمادگی-امتحان"
TITLE="کلاس خصوصی ریاضی در ایذه | تدریس مفهومی، رفع اشکال و آمادگی امتحان"
SHA="4b9ef2c11ce6ee8ee54fbb8138459a454619f3a46d85fb0b900b960ea3777fea"
def read():return tool("get_content",{"type":"article","id":ID})["record"]
before=read()
assert int(before["id"])==ID and before["title"]==TITLE and int(before["status"])==1
assert before["slug"] in (SLUG,ACCIDENTAL)
assert "09168949864" in before["content"] and before["content"].count("<img ")==2
assert before["img"]=="them/admin/dist/img/img-bcf4798163a92a7fb5332e9e94bfd3d3.webp"
url="https://alotamiratchi.ir/"+before["img"]
with urllib.request.urlopen(url,timeout=30) as response:
    b=response.read(40000)
assert len(b)==29094 and hashlib.sha256(b).hexdigest()==SHA
if before["slug"]!=SLUG:
    result=tool("update_content",{
      "type":"article","id":ID,"fields":{"slug":SLUG},"confirm_public":True
    })["record"]
    assert int(result["id"])==ID
after=read()
assert after["slug"]==SLUG and after["title"]==TITLE and after["img"]==before["img"]
for key in ("id","title","content","description","keyword","tags","contact_number","post_id","user_id","status"):
    assert str(after.get(key))==str(before.get(key)),key
print("FEATURED_WEBP_SHA256_VERIFIED",len(b),SHA)
print("SLUG_RESTORED_AND_NO_OTHER_FIELD_CHANGED",after["slug"])
page="https://alotamiratchi.ir/post/508/"+SLUG
with urllib.request.urlopen(urllib.request.Request(page,headers={"User-Agent":"Mozilla/5.0"}),timeout=30) as response:
    html=response.read(600000).decode("utf-8","replace")
    assert response.status==200
assert TITLE in html and "09168949864" in html
print("PUBLISHED_URL_VERIFIED",page)
print("FINAL_SUCCESS_ID",ID,"FEATURED",after["img"],"INLINE_IMAGES",after["content"].count("<img "))
