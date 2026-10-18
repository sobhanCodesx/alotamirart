"""Verify and publish only the user-approved water-odor article 506."""
import json,sys
from pathlib import Path
sys.path.insert(0,'scripts')
from mcp_content_job import tool
p=json.loads(Path('editorial/triangle-alo-20261018.json').read_text())
expected=p['fields'];slug=expected['slug']
assert slug=='refrigerator-dispenser-water-bad-taste-odor-diagnosis'
a=tool('get_content',{'type':'article','id':506})['record']
for k in ('title','slug','content','description'):
    assert a[k].strip()==expected[k].strip(),'Saved field differs: '+k
assert int(a['post_id'])==9 and a.get('img')
assert a['content'].count('<img ')==2 and a['content'].count('<a ')==5
assert '{{' not in a['content']
print('DRAFT_READBACK_VERIFIED',a['id'],a['slug'],a['img'])
if int(a.get('status') or 0)!=1:
    tool('set_content_published',{'type':'article','id':506,'published':True,'confirm':True})
a=tool('get_content',{'type':'article','id':506})['record']
assert int(a['status'])==1 and a['content'].strip()==expected['content'].strip()
print('PUBLISHED_READBACK_VERIFIED','https://alotamiratchi.ir/post/506')
