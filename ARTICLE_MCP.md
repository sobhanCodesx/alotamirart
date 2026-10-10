# AloTamirArt private article MCP

Inspired by `game-shop`'s `CONTENT_AGENT_MCP.md` and `/api/mcp`, adapted to this legacy PHP/Apache application. The existing admin article form, database tables, URLs and routes remain unchanged.

## Deploy

1. **Security prerequisite:** Production database credentials are currently committed in `config/database.php`. Rotate **all** exposed credentials, remove them from Git history where feasible, and move secrets into protected environment variables before enabling this endpoint. Check tracked `error_log` for additional secrets.
2. Deploy `api/mcp.php` to the server. Apache's existing `.htaccess` serves real files directly, so the endpoint is `https://YOUR_DOMAIN/api/mcp.php`.
3. Requires PHP 8.1+, PDO MySQL and mbstring. No Composer/Laravel, daemon, or database migration.
4. Configure **server-side** environment variables (never in Git or a web-readable file):
   - `MCP_API_TOKEN`: random secret, at least 32 characters (generate with `php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"`).
   - `MCP_AUTHOR_USER_ID`: existing valid admin/editor user id in `users`.
   - `MCP_ALLOW_PUBLISH`: `0` by default, change to `1` only if explicit publishing is intended.
5. Set HTTPS and restrict access to the API at the web-server/WAF layer where possible. Ensure the Authorization header reaches PHP. Rate-limit requests. Back up production data before deployment.
6. **Validate actual production `posts` schema first.** The endpoint assumes `posts` columns `id,title,slug,content,description,keyword,tags,post_id,user_id,status,created_at,updated_at`, matching current application conventions. Adjust SQL for any schema differences; this has not been tested against your production database.
7. Connect this HTTPS MCP URL as a **custom MCP app/connector** in ChatGPT if available on your account/workspace, with Bearer authentication configured privately. Adding GitHub alone does not connect the new MCP server.

## Tools

- `list_article_categories` — category ids and titles from `menu`
- `list_articles` — latest article metadata including unpublished entries
- `get_article` — full article by id
- `create_article_draft` — insert into `posts` with `status=0`
- `update_article_draft` — edit only `status=0` posts
- `publish_article` — requires explicit `confirm=true` and `MCP_ALLOW_PUBLISH=1`

No delete tool or arbitrary SQL. Media upload is deliberately excluded in v1. Drafts have no required featured image; attach one through the existing admin interface before publishing if needed. Avoid giving public access to the MCP token.

## Smoke tests

```bash
curl -i https://YOUR_DOMAIN/api/mcp.php
# Expected: HTTP 405

curl -i -X POST https://YOUR_DOMAIN/api/mcp.php -H 'Content-Type: application/json' -d '{"jsonrpc":"2.0","id":1,"method":"tools/list"}'
# Expected: HTTP 401

curl -X POST https://YOUR_DOMAIN/api/mcp.php \
  -H "Authorization: Bearer $MCP_API_TOKEN" \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{"jsonrpc":"2.0","id":1,"method":"tools/list","params":{}}'
```

After connection, ask ChatGPT: "دسته‌بندی‌های مقالات الو تعمیراتچی رو نشون بده" and then "یک پیش‌نویس مقاله درباره تعمیر یخچال بساز". Publishing is always a separate explicit step.

## Notes

- This is a minimal JSON-RPC MCP tool endpoint with legacy `initialize` and `tools/list`/`tools/call` support, not a complete Streamable HTTP session/SSE implementation. Verify connector compatibility before relying on it.
- Existing `game-shop` implementation uses Laravel and additional read-only GraphQL intelligence. This PHP v1 intentionally limits scope to article management.
- No production deployment or live integration test has been performed.

## Expanded content tools (v2)

The MCP entrypoint now also loads `api/content-tools.php`, which adds typed content-management tools corresponding to the existing PHP admin form fields.

| type | DB table | Supported form fields |
|---|---|---|
| `article` | `posts` | title, slug, content, description, keyword, tags, contact_number, post_id |
| `brand_article` | `post_brand` | title, slug (ASCII), content, des, tags, contact_number, brand_id |
| `brand` | `items_brands` | name, des |
| `category` | `menu` | title, description, sort |

The optional `image_base64` field in `create_content` and `update_content` accepts a base64-encoded JPEG/PNG/WebP image (maximum decoded size 2.5MB). It is stored under the same `them/admin/dist/img/` structure used by the legacy admin interface. Image uploads to new brand and brand-article records are mandatory, reflecting their admin forms. Article featured images are optional.

Tools:
- `describe_content_fields`: discover available types and allowed fields
- `list_content`: list available records by type and IDs
- `get_content`: read full record (including all stored fields)
- `create_content`: create draft article/brand article or explicitly confirmed public brand/category
- `update_content`: modify a draft; public records require `confirm_public=true`
- `set_content_published`: explicitly publish or unpublish article or brand article; publishing also requires `MCP_ALLOW_PUBLISH=1`

Example tool call (draft):

```json
{"jsonrpc":"2.0","id":10,"method":"tools/call","params":{"name":"create_content","arguments":{"type":"article","fields":{"title":"راهنمای تعمیر یخچال","content":"<h2>مقدمه</h2><p>متن مقاله</p>","description":"خلاصه برای نتایج جستجو","keyword":"تعمیر یخچال","tags":"یخچال, تعمیر","contact_number":"02100000000","post_id":1}}}}
```

Use `list_content` with `type=category` to find existing `post_id`; for brand articles, list `type=brand` to resolve `brand_id`. The caller must provide an image for creating a brand article and should verify the published URL and image after real deployment.

The endpoint discovers real columns using `SHOW COLUMNS` so unsupported fields are rejected, not silently inserted. Only explicitly allowlisted form fields may be written; author and publication status cannot be spoofed via `fields`.

**Limits:** This implements all fields identified in these four existing admin content forms, not all modules on the site. City management is disabled in the current legacy admin implementation and requires separate design. It does not auto-generate images, autonomously assess SEO quality, scrape sources, or expose arbitrary database writes. The AI can compose rich HTML and SEO metadata before calling the tool. A separate MCP connection must be configured and tested on the deployed HTTPS endpoint; a GitHub-only connection cannot directly call this PHP MCP.
