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
