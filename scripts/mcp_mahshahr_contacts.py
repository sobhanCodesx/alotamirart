#!/usr/bin/env python3
"""Targeted, verified contact-number update for all Mahshahr editorial posts.

Fetch every matching title from the private MCP graph, including drafts, and
update ONLY contact_number. Fails closed if graph results are truncated.
Do not print full records or draft titles into public GitHub Actions logs.
"""
import re

CITY = "ماهشهر"
TYPES = (("article", "articles"), ("brand_article", "brand_articles"))


def apply_mahshahr_contacts(tool, request):
    if request.get("action") != "bulk_mahshahr_contacts":
        raise ValueError("Unexpected bulk operation")
    if request.get("city") != CITY or request.get("confirm") is not True:
        raise ValueError("Explicit Mahshahr scope and confirmation required")
    phone = request.get("phone")
    if not isinstance(phone, str) or re.fullmatch(r"09[0-9]{9}", phone) is None:
        raise ValueError("Expected an Iranian mobile number, e.g. 09123456789")

    plan = []
    seen = set()
    # Discover both published and draft city-targeted articles, including brand posts.
    for content_type, graph_key in TYPES:
        result = tool("query_alo_graph", {
            "type": content_type, "query": CITY,
            "limit": 25, "include_drafts": True,
        })
        records = result["entities"][graph_key]
        if not isinstance(records, list) or len(records) >= 25:
            raise RuntimeError("Mahshahr list incomplete; refusing bulk write")
        for result_record in records:
            content_id = int(result_record["id"])
            key = (content_type, content_id)
            if content_id <= 0 or key in seen:
                raise ValueError("Invalid or duplicate Mahshahr article")
            seen.add(key)
            # Verify exact record from DB, not just a search summary.
            record = tool("get_content", {"type": content_type, "id": content_id})["record"]
            if not isinstance(record, dict) or int(record.get("id", 0)) != content_id:
                raise RuntimeError("Article changed during discovery")
            if CITY not in str(record.get("title") or ""):
                raise RuntimeError("Article title no longer matches Mahshahr; refusing write")
            if "contact_number" not in record:
                raise RuntimeError("Production contact_number column unavailable; refusing write")
            plan.append((content_type, content_id, record["contact_number"]))

    if not plan:
        raise RuntimeError("No Mahshahr articles identified; refusing silent no-op")

    changed = []
    already = []
    for content_type, content_id, existing_phone in plan:
        if str(existing_phone or "").strip() == phone:
            already.append((content_type, content_id))
            continue
        args = {
            "type": content_type, "id": content_id,
            "fields": {"contact_number": phone}, "confirm_public": True
        }
        updated = tool("update_content", args)["record"]
        if str(updated.get("contact_number") or "") != phone:
            raise RuntimeError("MCP update did not save chosen number")
        reread = tool("get_content", {"type": content_type, "id": content_id})["record"]
        if str(reread.get("contact_number") or "") != phone:
            raise RuntimeError("Persisted contact number verification failed")
        changed.append((content_type, content_id))
        print("MAHSHahr_PHONE_UPDATED", content_type, content_id)
    print("MAHSHahr_PHONE_VERIFIED_OK", "matched", len(plan), "updated", len(changed),
          "already_correct", len(already))
    return {"matched": len(plan), "updated": changed, "already_correct": already}
