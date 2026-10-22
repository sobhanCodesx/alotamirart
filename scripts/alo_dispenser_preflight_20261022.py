"""Read-only preflight for one approved laundry drawer article; no content writes."""
import sys,json,re,urllib.request
sys.path.insert(0,'scripts')
import mcp_content_job as m
m.ENDPOINT='https://www.alotamiratchi.ir/api/mcp/'
slug='washing-machine-detergent-softener-drawer-residue'
print('SCHEMA',json.dumps(m.tool('describe_content_fields',{}),ensure_ascii=False))
print('CATEGORIES',json.dumps(m.tool('list_article_categories',{}),ensure_ascii=False))
a=m.tool('find_content',{'type':'article','slug':slug})['record']
print('EXACT_EXISTING',json.dumps({k:a.get(k) for k in ('id','title','slug','status','user_id','img')} if a else None,ensure_ascii=False))
print('RECENT_TITLES',json.dumps(m.tool('list_content',{'type':'article','limit':50}),ensure_ascii=False))
for url in ('https://www.alotamiratchi.ir/sitemap.xml','https://www.alotamiratchi.ir/sitemap.php','https://www.alotamiratchi.ir/post/503','https://www.alotamiratchi.ir/post/489','https://www.alotamiratchi.ir/post/497','https://www.alotamiratchi.ir/'):
 try:
  req=urllib.request.Request(url,headers={'User-Agent':'Alo-Editorial-Verification/1.0'})
  with urllib.request.urlopen(req,timeout=25) as r:
   raw=r.read(2000000).decode('utf-8','replace')
   print('PUBLIC',json.dumps({'requested':url,'url':r.url,'status':r.status,'bytes':len(raw.encode()),'title':re.findall(r'<title[^>]*>(.*?)</title>',raw,re.S|re.I)[:1],'h1':re.findall(r'<h1[^>]*>(.*?)</h1>',raw,re.S|re.I)[:2],'descriptions':re.findall(r'<meta[^>]+name=["\\\']description["\\\'][^>]*>',raw,re.I),'article_links':re.findall(r'<loc>(.*?)</loc>',raw)[:15]},ensure_ascii=False))
 except Exception as e: print('PUBLIC_ERROR',url,type(e).__name__,str(e)[:160])
