#!/usr/bin/env python3
"""Read public AloTamiratchi site context via private MCP; never log drafts."""
import json
import os
from pathlib import Path
import sys
import urllib.error
import urllib.request

ROOT=Path(__file__).resolve().parents[1]
URL="https://www.alotamiratchi.ir/api/mcp/"

def validate(job):
    if not isinstance(job,dict) or job.get("tool") not in ("query_alo_graph","describe_alo_graph"):
        raise ValueError("Only read-only graph tools are allowed")
    args=job.get("arguments",{})
    if not isinstance(args,dict) or set(args)-{"type","query","limit","include_drafts"}:
        raise ValueError("Invalid public graph arguments")
    if "include_drafts" in args and args["include_drafts"] is not False:
        raise ValueError("Never expose private drafts in public GitHub logs")
    if args.get("type","all") not in ("all","article","brand_article","brand","category"):
        raise ValueError("Invalid graph content type")
    limit=args.get("limit",10)
    if not isinstance(limit,int) or isinstance(limit,bool) or limit<1 or limit>25:
        raise ValueError("Limit must be 1..25")
    q=args.get("query","")
    if not isinstance(q,str) or len(q)>100:
        raise ValueError("Query must be <=100 characters")
    return {"name":job["tool"],"arguments":args}

def run(relative):
    path=(ROOT/relative).resolve()
    if path.parent!=(ROOT/"content-queries").resolve() or path.suffix!=".json":
        raise ValueError("Job must live under content-queries/*.json")
    request=validate(json.loads(path.read_text(encoding="utf-8")))
    token=os.getenv("MCP_API_TOKEN","")
    if len(token)<32:
        raise RuntimeError("Repository MCP_API_TOKEN secret not configured")
    payload=json.dumps({"jsonrpc":"2.0","id":1,"method":"tools/call","params":request},ensure_ascii=False).encode("utf-8")
    query=urllib.request.Request(URL,data=payload,method="POST",headers={
        "Authorization":"Bearer "+token,"Content-Type":"application/json",
        "Accept":"application/json","User-Agent":"AloTamiratchi-Context/1.0",
    })
    try:
        with urllib.request.urlopen(query,timeout=40) as response:
            reply=json.load(response)
    except urllib.error.HTTPError as e:
        raise RuntimeError("MCP public content query returned HTTP "+str(e.code)) from None
    if "error" in reply or (reply.get("result") or {}).get("isError"):
        raise RuntimeError("MCP public content read failed")
    context=(reply.get("result") or {}).get("structuredContent")
    if not isinstance(context,dict):
        raise RuntimeError("MCP returned malformed graph context")
    print("ALO_PUBLIC_GRAPH_RESULT_START")
    print(json.dumps(context,ensure_ascii=False,separators=(",",":")))
    print("ALO_PUBLIC_GRAPH_RESULT_END")

if __name__=="__main__":
    if len(sys.argv)!=2:
        raise SystemExit("Provide content-queries/*.json")
    run(sys.argv[1])
