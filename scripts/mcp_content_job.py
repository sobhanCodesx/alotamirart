#!/usr/bin/env python3
"""Publish a user-requested article through the real MCP API from GitHub Actions.

Only a content-requests/*.json commit triggers the workflow; this script
never edits website code, pushes to main, or calls the deployment endpoint.
"""
import json
import os
import pathlib
import re
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


def load_local_image(relative_path):
    """Read only approved repository images and optimize to WebP."""
    import base64
    from io import BytesIO
    from PIL import Image, ImageOps
    if not isinstance(relative_path, str) or not relative_path.startswith("editorial-images/"):
        raise ValueError("image_path must be inside editorial-images/")
    image_path = (ROOT / relative_path).resolve()
    allowed = (ROOT / "editorial-images").resolve()
    if not image_path.is_relative_to(allowed) or not image_path.is_file():
        raise ValueError("Invalid or missing editorial image")
    if image_path.suffix.lower() not in (".webp", ".png", ".jpg", ".jpeg"):
        raise ValueError("Unsupported editorial image type")
    if image_path.stat().st_size > 8 * 1024 * 1024:
        raise ValueError("Editorial image exceeds 8 MB")
    with Image.open(image_path) as source:
        im = ImageOps.exif_transpose(source).convert("RGB")
        im.thumbnail((1200, 1200), Image.Resampling.LANCZOS)
        for quality in (82, 72, 62, 52, 42, 32, 22, 14):
            buf = BytesIO()
            im.save(buf, "WEBP", quality=quality, method=6)
            if len(buf.getvalue()) <= 30720:
                print("EDITORIAL_IMAGE_WEBP_BYTES", len(buf.getvalue()))
                return {"image_base64": base64.b64encode(buf.getvalue()).decode("ascii")}
    raise ValueError("Unable to compress editorial image below 30 KiB")


def media_args(request):
    if request.get("image_path"):
        if any(request.get(k) for k in ("banner", "image_url", "image_base64")):
            raise ValueError("image_path conflicts with another featured image")
        return load_local_image(request["image_path"])
    if request.get("banner") is not None:
        if request.get("image_url") or request.get("image_base64"):
            raise ValueError("Editorial banner conflicts with provided featured image")
        from alo_banner import make_banner
        data, size = make_banner(request["banner"])
        print("BANNER_GENERATED_BYTES", size)
        return {"image_base64": data}
    sources=[key for key in ("image_url","image_base64") if request.get(key)]
    if len(sources)>1:
        raise ValueError("Only one featured image source is permitted")
    if not sources:
        raise ValueError("A unique, topic-relevant featured image is required")
    if sources[0] == "image_url":
        # Optimize downloaded artwork before sending it to the CMS.
        import base64
        from io import BytesIO
        from PIL import Image, ImageOps
        req = urllib.request.Request(request["image_url"], headers={"User-Agent": "AloTamiratchi-ImageOptimizer/1.0"})
        with urllib.request.urlopen(req, timeout=30) as response:
            raw = response.read(8 * 1024 * 1024 + 1)
        if len(raw) > 8 * 1024 * 1024:
            raise ValueError("Source image exceeds the 8 MB safety limit")
        im = Image.open(BytesIO(raw))
        im = ImageOps.exif_transpose(im).convert("RGB")
        for width in (1100, 960, 840, 720, 600, 480):
            height = max(1, round(im.height * width / im.width))
            scaled = im.resize((width, height), Image.Resampling.LANCZOS)
            for quality in (76, 65, 55, 45, 35, 25, 15):
                out = BytesIO()
                scaled.save(out, "WEBP", quality=quality, method=6)
                if len(out.getvalue()) <= 30720:
                    print("FEATURED_IMAGE_OPTIMIZED_BYTES", len(out.getvalue()))
                    return {"image_base64": base64.b64encode(out.getvalue()).decode("ascii")}
        raise ValueError("Cannot optimize featured image to 30 KB")
    if sources[0] == "image_base64":
        import base64
        if len(base64.b64decode(request["image_base64"], validate=True)) > 30720:
            raise ValueError("Featured image exceeds 30 KB")
    return {sources[0]:request[sources[0]]}



def create_content_with_optional_field_fallback(content_type, fields, media, **kwargs):
    """Skip only unavailable optional columns on legacy live databases."""
    remaining = dict(fields)
    removable = {"keyword", "tags", "contact_number"}
    for _ in range(len(removable) + 1):
        try:
            return tool("create_content", {
                "type": content_type, "fields": remaining, **media, **kwargs
            })
        except RuntimeError as error:
            match = re.fullmatch(
                r"Unknown/unavailable content field: ([a-z_]+)", str(error)
            )
            field = match.group(1) if match else None
            if field not in removable or field not in remaining:
                raise
            del remaining[field]
            print("OPTIONAL_LEGACY_COLUMN_SKIPPED", field)
    raise RuntimeError("Could not match optional columns")


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
    # Reject repeated stock photos across editorial publication requests.
    image_url = request.get("image_url")
    if image_url:
        for other in sorted(path.parent.glob("*.json")):
            if other == path:
                continue
            try:
                prior = json.loads(other.read_text(encoding="utf-8"))
            except (OSError, ValueError):
                continue
            if (prior.get("type") == "article" and
                prior.get("action", "publish") == "publish" and
                prior.get("image_url") == image_url):
                raise ValueError("Duplicate featured image URL: " + other.name +
                                 "; choose a unique relevant image for this article")
    print("Content task:", path.name, "slug:", slug)

    # Idempotent re-runs: publishing a draft after a disabled publish attempt
    # must NOT create another copy of the article.
    existing = tool("find_content", {"type": "article", "slug": slug})["record"]

    if existing:
        article_id = int(existing["id"])
        print("Reusing existing matching article:", article_id)
        if request.get("author_username"):
            raise RuntimeError("Existing slug requires explicit author verification; refusing to reuse it")
        if int(existing.get("status") or 0) == 1:
            print("ALREADY_PUBLISHED", "https://alotamiratchi.ir/post/" + str(article_id) + "/" + slug)
            return
        if not existing.get("img"):
            existing = tool("update_content", {
                "type": "article", "id": article_id,
                "fields": {"title": existing["title"]}, **media
            })["record"]
    else:
        created = create_content_with_optional_field_fallback("article", fields, media, **({"author_username": request["author_username"]} if request.get("author_username") else {}))
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
    if action=="inspect_dishwasher_categories_20261030":
        result=tool("list_article_categories",{})
        print("ALO_DISHWASHER_CATEGORIES",json.dumps(result,ensure_ascii=False))
        return
    if action == "inspect_gilan_96_phones":
        result = tool("set_gilan_96_article_phones", {
            "profile_user_id":96, "profile_username":"gilan",
            "phone":"09040731535", "confirm":True,
            "inspect_only":True,
        })
        if result.get("success") is not True or result.get("inspect_only") is not True:
            raise RuntimeError("Profile inspection failed")
        print("GILAN_96_INSPECTION",json.dumps(result.get("checks"),ensure_ascii=False))
        return
    if action == "update_gilan_96_phones":
        if request.get("profile_user_id") != 96 or request.get("profile_username") != "gilan" or request.get("phone") != "09040731535" or request.get("confirm") is not True:
            raise ValueError("Exact profile identity, destination phone and confirmation are required")
        result = tool("set_gilan_96_article_phones", {
            "profile_user_id":96, "profile_username":"gilan",
            "phone":"09040731535", "confirm":True
        })
        if result.get("success") is not True or result.get("owner_verified") is not True or result.get("user_id") != 96:
            raise RuntimeError("Profile article contact update was not verified")
        counts=result.get("results") or {}
        print("GILAN_96_PHONE_VERIFIED_OK", "articles",counts.get("articles"),
              "brand_articles",counts.get("brand_articles"),
              "changed_articles",counts.get("changed_articles"),
              "changed_brand_articles",counts.get("changed_brand_articles"),
              "fields_changed",counts.get("fields_changed"))
        return
    if action == "bulk_shahrekord_contacts":
        if request.get("city") != "شهرکرد" or request.get("phone") != "09139858891" or request.get("confirm") is not True:
            raise ValueError("Exact Shahrekord scope and authorized phone required")
        result = tool("set_shahrekord_contact_numbers", {"city":"شهرکرد","phone":"09139858891","confirm":True})
        if result.get("verified") is not True or result.get("phone") != "09139858891":
            raise RuntimeError("Server did not confirm Shahrekord contact update")
        print("SHAHREKORD_ALL_CONTACTS_VERIFIED",json.dumps(result,ensure_ascii=False))
        return
    if action == "bulk_mahshahr_contacts":
        if request.get("city") != "ماهشهر" or request.get("confirm") is not True:
            raise ValueError("Mahshahr operation requires explicit city confirmation")
        phone = request.get("phone")
        if not isinstance(phone, str) or not re.fullmatch(r"09[0-9]{9}", phone):
            raise ValueError("Invalid Iranian mobile phone")
        result = tool("set_mahshahr_contact_numbers", {
            "city": "ماهشهر", "phone": phone, "confirm": True,
        })
        if result.get("verified") is not True:
            raise RuntimeError("Server did not verify the Mahshahr contact change")
        counts = result.get("matched") or {}
        total = sum(int(counts.get(t, 0)) for t in ("article", "brand_article"))
        if total < 17:
            raise RuntimeError("Mahshahr contact update matched fewer posts than the public inventory")
        print("MAHSHahr_CONTACTS_VERIFIED_OK", json.dumps(result, ensure_ascii=False))
        return
    if action == "replace_refrigerator_o3am_links":
        if (request.get("category_id") != 9 or
            request.get("from_host") != "o3am.com" or
            request.get("to_host") != "o3am.ir" or
            request.get("confirm") is not True):
            raise ValueError("Explicit exact refrigerator-domain replacement request required")
        result = tool("replace_refrigerator_o3am_links", {
            "category_id": 9, "from_host": "o3am.com",
            "to_host": "o3am.ir", "confirm": True,
        })
        if result.get("verified") is not True or result.get("category_id") != 9:
            raise RuntimeError("Live refrigerator URL replacement verification failed")
        print("REFRIGERATOR_LINK_SWAP_VERIFIED_OK", json.dumps(result, ensure_ascii=False))
        return
    if content_type not in ("article","brand_article","brand","category"):
        raise ValueError("Invalid managed content type")
    if action=="publish" and content_type=="article":
        publish(path)
        return
    if action=="crosslink_console_513":
        if content_type!="article" or request.get("id")!=513 or request.get("expected_slug")!="connect-console-to-tv-monitor-hdmi-troubleshooting":
            raise ValueError("Wrong HDMI guide link request")
        record=tool("get_content",{"type":"article","id":513})["record"]
        old=record.get("content")
        if (record.get("slug")!=request["expected_slug"] or record.get("title")!="وصل کردن کنسول بازی به مانیتور تلویزیون" or
            int(record.get("status") or 0)!=1 or not record.get("img") or not isinstance(old,str)):
            raise RuntimeError("Article 513 identity, cover, or published status mismatch")
        old_url='https://khadamatrad.ir/'
        target='https://khadamatrad.ir/article/connect-game-console-to-monitor-tv-hdmi-174'
        needle='<a href="'+old_url+'">مطالب فنی تلویزیون در خدمات راد</a>'
        link='<a href="'+target+'">راهنمای اتصال کنسول به تلویزیون در خدمات راد</a>'
        if target in old:
            print("ALO_CONSOLE_DEEP_LINK_ALREADY_PRESENT",513)
            return
        if old.count(needle)!=1:raise RuntimeError("Expected TV repair anchor missing")
        content=old.replace(needle,link,1)
        tool("update_content",{"type":"article","id":513,"fields":{"content":content},"confirm_public":True})
        after=tool("get_content",{"type":"article","id":513})["record"]
        if (after.get("content")!=content or after.get("img")!=record.get("img") or
            after.get("slug")!=record.get("slug") or int(after.get("status") or 0)!=1):
            raise RuntimeError("Alo console crosslink not verified")
        if not all(x in content for x in [target,"https://tekanino.ir/blog/connect-game-console-to-monitor-tv-guide","https://playnexus.ir/"]):
            raise RuntimeError("One of required links missing")
        print("ALO_CONSOLE_CROSSLINKS_VERIFIED",513,"RAD",target)
        return
    if action=="crosslink_igniter_512":
        if content_type!="article" or request.get("id")!=512 or request.get("expected_slug")!="gas-stove-igniter-not-sparking-troubleshooting":
            raise ValueError("Wrong igniter crosslink target")
        old_record=tool("get_content",{"type":"article","id":512})["record"]
        old=old_record.get("content")
        if old_record.get("slug")!=request["expected_slug"] or int(old_record.get("status") or 0)!=1 or not old_record.get("img") or not isinstance(old,str):
            raise RuntimeError("Published igniter identity or featured image unavailable")
        new=old
        snippets=[
            ("از فلز تیز، سنباده و مایع فراوان برای پاک‌کردن مجموعه الکتریکی استفاده نکنید.",
             'از فلز تیز، سنباده و مایع فراوان برای پاک‌کردن مجموعه الکتریکی استفاده نکنید. <a href="https://khadamatrad.ir/article/gas-stove-igniter-no-spark-clicking-causes-173">راهنمای بررسی جرقه نزدن فندک در خدمات راد</a> نیز به تفاوت آلودگی و خرابی قطعات اشاره می‌کند.'),
            ("اگر جرقه‌زنی ناخواسته ادامه دارد، برق را به شیوه ایمن قطع کنید.",
             'اگر جرقه‌زنی ناخواسته ادامه دارد، برق را به شیوه ایمن قطع کنید. برای شناخت علائم تیک‌تیک مداوم، <a href="https://tekanino.ir/blog/gas-stove-igniter-not-sparking-clicking-reasons">آموزش خرابی فندک در تکانی‌نو</a> را مطالعه کنید.')]
        if "gas-stove-igniter-no-spark-clicking-causes-173" in new:
            if all(url in new for url in ["gas-stove-igniter-no-spark-clicking-causes-173","gas-stove-igniter-not-sparking-clicking-reasons"]):
                print("ALO_IGNITER_CROSSLINKS_ALREADY_PRESENT",512);return
            raise RuntimeError("Partial existing igniter crosslink")
        for prev,replacement in snippets:
            if new.count(prev)!=1:raise RuntimeError("Cannot safely match igniter section")
            new=new.replace(prev,replacement,1)
        tool("update_content",{"type":"article","id":512,"fields":{"content":new},"confirm_public":True})
        after=tool("get_content",{"type":"article","id":512})["record"]
        if after.get("content")!=new or after.get("img")!=old_record.get("img") or int(after.get("status") or 0)!=1:
            raise RuntimeError("Igniter crosslinks verification failed")
        print("ALO_IGNITER_CROSSLINKS_VERIFIED",512,2,"FEATURED_PRESERVED")
        return
    if action=="add_babol_post10_internal":
        if content_type!="article" or request.get("id")!=511 or request.get("expected_slug")!="gas-stove-thermocouple-symptoms-safe-checks":
            raise ValueError("Unexpected Babol link target")
        before=tool("get_content",{"type":"article","id":511})["record"]
        old=before.get("content")
        if before.get("slug")!=request["expected_slug"] or int(before.get("status") or 0)!=1 or not before.get("img") or not isinstance(old,str):
            raise RuntimeError("Live article identity or image mismatch")
        target="https://alotamiratchi.ir/post/10"
        if target in old:
            print("ALO_BABOL_POST10_ALREADY_PRESENT",511)
            return
        context="دور زدن ترموکوپل یا حذف شیر ایمنی برای روشن نگه داشتن شعله اقدامی خطرناک است و نباید انجام شود."
        if old.count(context)!=1:raise RuntimeError("Expected repair context not found")
        extra=' برای اطلاعات خدمات محلی، <a href="'+target+'">صفحه تعمیر اجاق گاز در بابل</a> را نیز ببینید و جزئیات ارائه خدمات را همان‌جا بررسی کنید.'
        new=old.replace(context,context+extra,1)
        tool("update_content",{"type":"article","id":511,"fields":{"content":new},"confirm_public":True})
        after=tool("get_content",{"type":"article","id":511})["record"]
        if after.get("content")!=new or after.get("img")!=before.get("img") or int(after.get("status") or 0)!=1:
            raise RuntimeError("Babol internal link not verified")
        print("ALO_BABOL_POST10_VERIFIED",511)
        return
    if action=="distribute_thermocouple_links":
        if content_type!="article" or request.get("id")!=511 or request.get("expected_slug")!="gas-stove-thermocouple-symptoms-safe-checks":
            raise ValueError("Only verified thermocouple article 511 is supported")
        article=tool("get_content",{"type":"article","id":511})["record"]
        if article.get("slug")!=request["expected_slug"] or int(article.get("status") or 0)!=1 or not article.get("img"):
            raise RuntimeError("Published article identity or image mismatch")
        original=article["content"]
        if not isinstance(original,str) or "<h2>ترموکوپل اجاق گاز چیست؟</h2>" not in original:
            raise ValueError("Unexpected article content")
        new=original
        # Move previous grouped link sections into the relevant passages.
        for header in ("<h2>خدمات مرتبط و مطالعه تکمیلی</h2>","<h2>مطالب مرتبط با خرابی ترموکوپل اجاق گاز</h2>"):
            if header in new:
                start=new.index(header)
                next_header=new.find("<h2>",start+len(header))
                if next_header<0:raise ValueError("No end boundary for grouped links")
                new=new[:start]+new[next_header:]
        snippets=[
            ("هرگز ولوم را با ابزار در حالت روشن ثابت نکنید.</p>",
             'هرگز ولوم را با ابزار در حالت روشن ثابت نکنید. برای تشخیص علت‌های مشابه، <a href="https://khadamatrad.ir/article/gas-stove-flame-goes-out-thermocouple-checks-172">راهنمای علت خاموش شدن شعله در خدمات راد</a> را ببینید.</p>'),
            ("برای اصلاح شکل شعله، نازل یا مسیر گاز را دستکاری نکنید.</p>",
             'برای اصلاح شکل شعله، نازل یا مسیر گاز را دستکاری نکنید. دیگر نشانه‌های اجاق را می‌توانید در <a href="https://alotamiratchi.ir/posts/categories/3/1">دسته مطالب اجاق گاز الو تعمیراتچی</a> بررسی کنید.</p>'),
            ("هیچ‌کدام از این نشانه‌ها به‌تنهایی مجوز باز کردن اتصالات گاز نیست.</p>",
             'هیچ‌کدام از این نشانه‌ها به‌تنهایی مجوز باز کردن اتصالات گاز نیست. برای مقایسه عملکرد حسگر و فندک، <a href="https://tekanino.ir/blog/gas-stove-thermocouple-failure-diagnosis">راهنمای خرابی ترموکوپل در تکانی‌نو</a> را بخوانید.</p>'),
            ("اگر مشکل ادامه داشت، پیش از آزمون‌های مکرر دستگاه را برای بررسی تخصصی متوقف کنید.</p>",
             'اگر مشکل ادامه داشت، پیش از آزمون‌های مکرر دستگاه را برای بررسی تخصصی متوقف کنید. درباره رسیدگی تخصصی به این دستگاه، <a href="https://tekanino.ir/gaz">صفحه خدمات تعمیر اجاق گاز</a> اطلاعات تکمیلی دارد.</p>'),
            ("دور زدن ترموکوپل یا حذف شیر ایمنی برای روشن نگه داشتن شعله اقدامی خطرناک است و نباید انجام شود.</p>",
             'دور زدن ترموکوپل یا حذف شیر ایمنی برای روشن نگه داشتن شعله اقدامی خطرناک است و نباید انجام شود. برای درخواست خدمات محلی <a href="https://tekanino.ir/gaz/babol">صفحه تعمیر اجاق گاز در بابل</a> را ببینید و شرایط پوشش خدمات را بررسی کنید.</p>')
        ]
        marker="href=\"https://khadamatrad.ir/article/gas-stove-flame-goes-out-thermocouple-checks-172\""
        if marker in new and "<h2>مطالب مرتبط با خرابی ترموکوپل اجاق گاز</h2>" not in original and "<h2>خدمات مرتبط و مطالعه تکمیلی</h2>" not in original:
            print("ALO_DISTRIBUTED_LINKS_ALREADY_PRESENT",511);return
        for old,replacement in snippets:
            if new.count(old)!=1:raise RuntimeError("Cannot safely locate unique paragraph for: "+old[:30])
            new=new.replace(old,replacement,1)
        if new.count("<a href=")<5:raise ValueError("Insufficient contextual links")
        tool("update_content",{"type":"article","id":511,"fields":{"content":new},"confirm_public":True})
        actual=tool("get_content",{"type":"article","id":511})["record"]
        if actual.get("content")!=new or actual.get("img")!=article.get("img") or actual.get("slug")!=article.get("slug") or int(actual.get("status") or 0)!=1:
            raise RuntimeError("Distributed links verification failed")
        print("ALO_THERMO_DISTRIBUTED_LINKS_VERIFIED",511,5,"IMAGE_PRESERVED")
        return
    if action=="append_related_links":
        # Read the live record before updating; never overwrite it with stale GitHub copy.
        if content_type!="article":
            raise ValueError("Related linking currently supports ordinary articles only")
        article_id=request.get("id")
        slug=request.get("expected_slug")
        section=request.get("section_html")
        if type(article_id) is not int or article_id<1 or not isinstance(slug,str) or not slug:
            raise ValueError("A positive article ID and exact slug are required")
        if not isinstance(section,str) or not 40<=len(section)<=3000:
            raise ValueError("Invalid linking section")
        if re.search(r"<(?!/?(?:p|a|h2|strong)\b)",section,re.I):
            raise ValueError("Unsupported HTML in related-links section")
        if re.search(r"\bon\w+\s*=|javascript:|<img",section,re.I):
            raise ValueError("Unsafe related-links content")
        urls=re.findall(r'href="([^"]+)"',section)
        if not urls or any(not url.startswith(("https://khadamatrad.ir/","https://tekanino.ir/","https://alotamiratchi.ir/")) for url in urls):
            raise ValueError("Related links must point to one of the three editorial sites")
        current=tool("get_content",{"type":"article","id":article_id})["record"]
        if current.get("slug")!=slug or int(current.get("status") or 0)!=1:
            raise ValueError("Article slug/status mismatch; refusing to change a different record")
        previous=current.get("content")
        if not isinstance(previous,str) or not current.get("img"):
            raise ValueError("Existing article HTML or cover is missing")
        header=section.split("</h2>",1)[0]+"</h2>"
        if header in previous:
            if all(url in previous for url in urls):
                print("RELATED_LINKS_ALREADY_PRESENT",article_id)
                return
            raise RuntimeError("Related links section already exists with different destinations")
        needle="<h2>جمع‌بندی</h2>"
        if previous.count(needle)!=1:
            raise ValueError("No unique safe anchor to append links")
        new_content=previous.replace(needle,section+"\n"+needle,1)
        tool("update_content",{
            "type":"article","id":article_id,
            "fields":{"content":new_content},
            "confirm_public":True
        })
        verified=tool("get_content",{"type":"article","id":article_id})["record"]
        if (int(verified.get("status") or 0)!=1 or
            verified.get("img")!=current.get("img") or
            verified.get("content")!=new_content or
            verified.get("slug")!=slug):
            raise RuntimeError("Related-links update failed live verification")
        print("RELATED_LINKS_VERIFIED",article_id,"added",len(urls))
        return
    # Text-only edits must keep the previously published featured image intact.
    has_media=any(request.get(key) is not None for key in ("banner","image_url","image_base64","image_path"))
    media=media_args(request) if action in ("draft","create","publish") or has_media else {}
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
            item=existing or create_content_with_optional_field_fallback("article", fields, media, **({"author_username": request["author_username"]} if request.get("author_username") else {}))["record"]
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
        if media and content_type in ("article", "brand_article"):
            verified = tool("get_content", {"type": content_type, "id": item_id})["record"]
            if not verified.get("img"):
                raise RuntimeError("Image update could not be verified")
            print("IMAGE_UPDATED_VERIFIED", content_type, item_id, verified["img"])
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
