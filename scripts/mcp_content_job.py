#!/usr/bin/env python3
"""Publish a user-requested article through the real MCP API from GitHub Actions.

Only a content-requests/*.json commit triggers the workflow; this script
never edits website code, pushes to main, or calls the deployment endpoint.
"""
import json
import os
import pathlib
import sys
import urllib.error
import urllib.request

ROOT = pathlib.Path(__file__).resolve().parents[1]
ENDPOINT = "https://alotamiratchi.ir/api/mcp/"
IMAGE_HOST = "https://upload.wikimedia.org/wikipedia/commons/"
TOKEN = os.environ.get("MCP_API_TOKEN", "")
if len(TOKEN) < 32:
    sys.exit("MCP_API_TOKEN repository secret missing")


def tool(name, arguments):
    data = json.dumps(
        {"jsonrpc": "2.0", "id": 1, "method": "tools/call",
         "params": {"name": name, "arguments": arguments}},
        ensure_ascii=False,
    ).encode("utf-8")
    req = urllib.request.Request(
        ENDPOINT, data=data, method="POST",
        headers={"Authorization": "Bearer " + TOKEN,
                 "Content-Type": "application/json; charset=utf-8",
                 "Accept": "application/json",
                 "User-Agent": "AloTamiratchi-GitHub-Publisher/2.1"},
    )
    try:
        with urllib.request.urlopen(req, timeout=40) as response:
            output = json.load(response)
    except urllib.error.HTTPError as e:
        snippet = e.read(2600).decode("utf-8", "replace")
        snippet = " ".join(snippet.split())
        # GET should return JSON 405 if PHP is reached. A 403 on GET too
        # identifies an upstream WAF/rate-limit/IP block.
        try:
            urllib.request.urlopen(ENDPOINT, timeout=12).close()
            diagnostic = "MCP GET: 200"
        except urllib.error.HTTPError as check:
            diagnostic = f"MCP GET: HTTP {check.code}"
        except Exception as check:
            diagnostic = "MCP GET unreachable: " + type(check).__name__
        raise RuntimeError(f"MCP HTTP error {e.code}; {diagnostic}; response: {snippet[:1300]}") from None
    except urllib.error.URLError as e:
        raise RuntimeError(f"MCP connection failed: {e.reason}") from None
    if "error" in output:
        raise RuntimeError("MCP protocol failure: " + str(output["error"].get("message", "")))
    result = output.get("result", {})
    if result.get("isError"):
        parts = result.get("content", [])
        message = parts[0].get("text", "unknown") if parts else "unknown"
        raise RuntimeError(str(message))
    parsed = result.get("structuredContent")
    if parsed is None:
        parts = result.get("content", [])
        parsed = json.loads(parts[0]["text"]) if parts else {}
    if not isinstance(parsed, dict):
        raise RuntimeError("MCP returned unexpected tool response")
    return parsed


def media_args(request):
    sources=[key for key in ("image_url","image_base64") if request.get(key)]
    if len(sources)>1:
        raise ValueError("Only one featured image source is permitted")
    return {sources[0]:request[sources[0]]} if sources else {}


def publish(path):
    request = json.loads(path.read_text(encoding="utf-8"))
    fields = request["fields"]
    media = media_args(request)
    slug = fields["slug"]
    if request.get("type") != "article" or not fields.get("post_id"):
        raise ValueError("This workflow supports normal articles with an existing category")
    if not all(isinstance(fields.get(k), str) and fields[k].strip()
               for k in ("title", "slug", "content", "description")):
        raise ValueError("Article title, slug, content and meta-description are required")
    print("Content task:", path.name, "slug:", slug)

    # Idempotent re-runs: publishing a draft after a disabled publish attempt
    # must NOT create another copy of the article.
    existing = tool("find_content", {"type": "article", "slug": slug})["record"]

    if existing:
        article_id = int(existing["id"])
        print("Reusing existing matching article:", article_id)
        if int(existing.get("status") or 0) == 1:
            print("ALREADY_PUBLISHED", "https://alotamiratchi.ir/post/" + str(article_id) + "/" + slug)
            return
        if not existing.get("img"):
            existing = tool("update_content", {
                "type": "article", "id": article_id,
                "fields": {"title": existing["title"]}, **media
            })["record"]
    else:
        created = tool("create_content", {
            "type": "article", "fields": fields, **media
        })
        existing = created["record"]
        article_id = int(existing["id"])
        print("DRAFT_CREATED", article_id)
    if not existing.get("img"):
        raise RuntimeError("Article does not have a featured image; not publishing")

    try:
        published = tool("set_content_published", {
            "type": "article", "id": article_id,
            "published": True, "confirm": True
        })["record"]
    except RuntimeError as e:
        print("ARTICLE_DRAFT_ID", article_id)
        raise RuntimeError("Article draft saved with featured photo but publication failed: " + str(e)) from None
    if int(published.get("status") or 0) != 1:
        raise RuntimeError("Publication did not set status=1")
    verified = tool("get_content", {"type": "article", "id": article_id})["record"]
    if int(verified.get("status") or 0) != 1 or not verified.get("img"):
        raise RuntimeError("Published record failed verification")
    print("PUBLISHED_ARTICLE_ID", article_id)
    print("PUBLISHED_URL", "https://alotamiratchi.ir/post/" + str(article_id) + "/" + slug)
    print("FEATURED_IMAGE_PATH", verified["img"])
    print("PUBLISH_VERIFIED_OK")


def manage_content(path):
    """Handle typed CRUD publication jobs; changes always use MCP allowlists."""
    request=json.loads(path.read_text(encoding="utf-8"))
    content_type=request.get("type","article")
    action=request.get("action","publish")
    if content_type not in ("article","brand_article","brand","category"):
        raise ValueError("Invalid managed content type")
    if action=="publish" and content_type=="article":
        publish(path)
        return
    media=media_args(request)
    fields=request.get("fields")
    if action in ("draft","create","publish"):
        if not isinstance(fields,dict):
            raise ValueError("Create requires fields object")
        if content_type in ("brand","category"):
            if action!="create":
                raise ValueError("Brands/categories require explicit create action")
            item=tool("create_content",{
                "type":content_type,"fields":fields,"confirm_public":True,**media
            })["record"]
            print("PUBLIC_CONTENT_CREATED",content_type,item["id"])
            return
        if content_type=="article":
            slug=fields.get("slug")
            existing=(tool("find_content",{"type":"article","slug":slug})["record"]
                      if isinstance(slug,str) and slug else None)
            item=existing or tool("create_content",{
                "type":"article","fields":fields,**media
            })["record"]
            if action=="draft":
                print("ARTICLE_DRAFT_ID",item["id"])
                return
        else:
            slug=fields.get("slug")
            if not isinstance(slug,str) or not slug:
                raise ValueError("Brand article slug required")
            existing=tool("find_content",{"type":"brand_article","slug":slug})["record"]
            item=existing or tool("create_content",{
                "type":"brand_article","fields":fields,**media
            })["record"]
            if action=="draft":
                print("BRAND_ARTICLE_DRAFT_ID",item["id"])
                return
        if int(item.get("status") or 0)!=1:
            item=tool("set_content_published",{
                "type":content_type,"id":int(item["id"]),"published":True,"confirm":True
            })["record"]
        if int(item.get("status") or 0)!=1:
            raise RuntimeError("MCP did not verify published state")
        print("PUBLISHED",content_type,item["id"])
        return
    if action in ("update","unpublish"):
        item_id=request.get("id")
        if not isinstance(item_id,int) or isinstance(item_id,bool) or item_id<1:
            raise ValueError("A positive record id is required")
        if action=="unpublish":
            if content_type not in ("article","brand_article"):
                raise ValueError("Only editorial content can be unpublished")
            item=tool("set_content_published",{
                "type":content_type,"id":item_id,"published":False,"confirm":True
            })["record"]
            print("UNPUBLISHED",content_type,item["id"])
            return
        if not isinstance(fields,dict) or not fields and not media:
            raise ValueError("Update requires fields or an image")
        item=tool("update_content",{
            "type":content_type,"id":item_id,"fields":fields or {},
            "confirm_public":True,**media
        })["record"]
        print("UPDATED",content_type,item["id"])
        return
    raise ValueError("Unsupported content management action")


if __name__ == "__main__":
    args = sys.argv[1:]
    if not args:
        sys.exit("Provide content-requests/*.json to publish")
    for arg in args:
        path = (ROOT / arg).resolve()
        if path.parent != (ROOT / "content-requests").resolve() or path.suffix != ".json":
            sys.exit("Refusing request outside content-requests/*.json")
        manage_content(path)
