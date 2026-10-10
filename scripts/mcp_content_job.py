#!/usr/bin/env python3
"""Publish a user-requested article through the real MCP API from GitHub Actions.

Only a content-requests/*.json commit triggers the workflow; this script
never edits website code, pushes to main, or calls the deployment endpoint.
"""
import base64
import json
import os
import pathlib
import sys
import urllib.error
import urllib.request

ROOT = pathlib.Path(__file__).resolve().parents[1]
ENDPOINT = "https://alotamiratchi.ir/api/mcp.php"
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
                 "Content-Type": "application/json",
                 "Accept": "application/json",
                 "User-Agent": "AloTamiratchi-Content/1.0"},
    )
    try:
        with urllib.request.urlopen(req, timeout=40) as response:
            output = json.load(response)
    except urllib.error.HTTPError as e:
        raise RuntimeError(f"MCP HTTP error {e.code}") from None
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


def load_image(url):
    if not isinstance(url, str) or not url.startswith(IMAGE_HOST):
        raise ValueError("Only Wikimedia Commons image URLs are supported")
    req = urllib.request.Request(url, headers={
        "User-Agent": "AloTamiratchi-Content/1.0 (featured-image upload)"
    })
    with urllib.request.urlopen(req, timeout=30) as response:
        mime = (response.headers.get("Content-Type") or "").lower()
        image = response.read(2500001)
    if not 1000 <= len(image) <= 2500000:
        raise RuntimeError("Featured image is outside 1KB-2.5MB range")
    if not image.startswith(b"\xff\xd8\xff"):
        raise RuntimeError("Source featured image is not a JPEG")
    return base64.b64encode(image).decode("ascii")


def publish(path):
    request = json.loads(path.read_text(encoding="utf-8"))
    fields = request["fields"]
    slug = fields["slug"]
    if request.get("type") != "article" or not fields.get("post_id"):
        raise ValueError("This workflow supports normal articles with an existing category")
    if not all(isinstance(fields.get(k), str) and fields[k].strip()
               for k in ("title", "slug", "content", "description")):
        raise ValueError("Article title, slug, content and meta-description are required")
    print("Content task:", path.name, "slug:", slug)

    # Idempotent re-runs: publishing a draft after a disabled publish attempt
    # must NOT create another copy of the article.
    records = tool("list_content", {"type": "article", "limit": 50})["records"]
    existing = None
    for record in records:
        if record.get("title") != fields["title"]:
            continue
        row = tool("get_content", {"type": "article", "id": int(record["id"])})["record"]
        if row.get("slug") == slug:
            existing = row
            break

    if existing:
        article_id = int(existing["id"])
        print("Reusing existing matching article:", article_id)
        if int(existing.get("status") or 0) == 1:
            print("ALREADY_PUBLISHED", "https://alotamiratchi.ir/post/" + str(article_id) + "/" + slug)
            return
        if not existing.get("img"):
            encoded = load_image(request["image_url"])
            existing = tool("update_content", {
                "type": "article", "id": article_id,
                "fields": {}, "image_base64": encoded
            })["record"]
    else:
        # Image is fetched and validated BEFORE inserting any content.
        encoded = load_image(request["image_url"])
        created = tool("create_content", {
            "type": "article", "fields": fields, "image_base64": encoded
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


if __name__ == "__main__":
    args = sys.argv[1:]
    if not args:
        sys.exit("Provide content-requests/*.json to publish")
    for arg in args:
        path = (ROOT / arg).resolve()
        if path.parent != (ROOT / "content-requests").resolve() or path.suffix != ".json":
            sys.exit("Refusing request outside content-requests/*.json")
        publish(path)
