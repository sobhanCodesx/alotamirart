"""Verify and publish only approved TV internet article 507."""
import json,sys
from pathlib import Path
sys.path.insert(0,'scripts')
from mcp_content_job import tool
p=json.loads(Path('editorial/tv-triangle-alo-20261018.json').read_text())
e=p['fields'];slug=e['slug']
assert slug=='smart-tv-wifi-connected-no-internet-diagnosis'
a=tool('get_content',{'type':'article','id':507})['record']
for k in ('title','slug','content','description'):
 assert a[k].strip()==e[k].strip(),'Saved field differs: '+k
assert int(a['post_id'])==2 and a.get('img')
assert a['content'].count('<img ')==2 and a['content'].count('<a ')==5
assert '{{' not in a['content'] and 'font-family:Lalezar' in a['content']
print('DRAFT_READBACK_VERIFIED',507,a['slug'],a['img'],'author_id',a.get('user_id'))
if int(a.get('status') or 0)!=1:
 tool('set_content_published',{'type':'article','id':507,'published':True,'confirm':True})
a=tool('get_content',{'type':'article','id':507})['record']
assert int(a['status'])==1 and a['content'].strip()==e['content'].strip()
print('PUBLISHED_READBACK_VERIFIED','https://alotamiratchi.ir/post/507')
