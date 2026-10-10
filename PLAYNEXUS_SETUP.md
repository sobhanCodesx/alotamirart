# AloTamiratchi — PlayNexus-style MCP and HTTPS deployment

Production remains **PHP 8.2 + MySQL + Apache on cPanel**. There is no SSH,
CMD, FTP/FTPS, Composer on the server, cron daemon, or long-running worker.

## Architecture

- **Read**: private Bearer MCP at `POST /api/mcp`, with `describe_alo_graph`
  and `query_alo_graph` for bounded relational article/category/brand context.
- **Write**: allowlisted MCP actions create/edit drafts, attach featured images,
  publish only on explicit `confirm=true` when `MCP_ALLOW_PUBLISH=1`.
- **Publisher**: GitHub queue files in `content-requests/*.json` launch the
  independent MCP content workflow. Content publishing never redeploys PHP.
  Duplicate slugs reuse the existing record.
- **Code**: pushes to `main` launch tests, create a one-time installation ZIP,
  then call the authenticated `/api/deployment-agent/?action=...` receiver.
- **Deployment**: HMAC-separated auth and signed manifest, HTTPS multipart
  chunks, source SHA, file hashes, safe allowlist, staged backup and rollback,
  ordered deploys, deletion verification and post-deploy health checks.
- **No arbitrary execution**: no remote shell, SQL execution tool, filesystem
  explorer, or access to credentials through MCP.

## The only required manual install

1. In GitHub Actions, open the latest successful **Deploy AloTamiratchi
   Production (signed HTTPS, no SSH/FTP)** run on `main`.
2. Under Artifacts download `alotamiratchi-bootstrap-<40-char-commit>`.
   Unzip the downloaded GitHub artifact locally, to obtain
   `alotamiratchi-bootstrap.zip`.
3. In cPanel **File Manager**, take a backup of the existing website and
   database. Upload `alotamiratchi-bootstrap.zip` into the SAME directory
   as the active `index.php` (often `public_html`). Extract it **there**,
   merging files, **not** deleting the existing directory. Verify that the
   project's `index.php` and `api/deployment-agent/index.php` are adjacent
   under the expected hierarchy. Extract once only.
4. The bootstrap intentionally excludes `.env` and runtime images in
   `them/admin/dist/img`; retain the existing media and live configuration.
   If `.env` does not exist, copy `.env.example` and configure its **private**
   values in cPanel File Manager. Do not paste credentials into chat/GitHub.
   Set the same long `MCP_API_TOKEN` as the existing GitHub Actions secret,
   valid `MCP_AUTHOR_USER_ID`, `MCP_ALLOW_PUBLISH=1` only if desired, and
   no other deployment secret or deployment enable switch is required.
5. Verify site pages and `GET /api/mcp` returns HTTP 405 (not HTML).
   `GET /api/deployment-agent/?action=ready` should return HTTP 401 without
   a token. After that, any future code change in GitHub on `main` runs
   automatic deployment. Content queue commits independently publish articles.

The first upload **installs the receiver**. No need to upload PHP after that.
Both API endpoints require valid Bearer credentials. If hosting WAF rejects
legitimate authenticated HTTPS requests, ask the hosting provider to allow the
specific authenticated routes, without disabling site-wide security.

## Invariants

- Never deploy `.env`, tokens, uploads, mutable production images or logs.
- Keep existing article URLs, table schemas, current admin forms and PHP
  route behavior; no migration is required.
- Manifest v2 covers both changed files and explicit deleted paths; a stale
  `base_commit` is rejected after the first successful deploy.
- Each deployment backs up affected files before any change, restores them
  on an unsuccessful switch or hash check, and validates source commit.
- If the receiver is not yet bootstrapped, GitHub reports
  `ALO_BOOTSTRAP_REQUIRED` and leaves the installation artifact available.
- Never claim deployment succeeded until the authenticated health probe
  verifies the exact commit plus an HTTP 200 homepage response.

## Quick verification

Run automatically in GitHub CI:
```
php tests/alo-site-graph-contract.php
php tests/playnexus-mcp-architecture.php
python3 -m unittest discover -s tests -p 'test_alo_release.py'
```

Publishing uses the same existing `MCP_API_TOKEN`; no second secret is created
for deployment. The derived HMAC signing and deployment-auth contexts are
different from the MCP Bearer token.

## Read-before-write from the connected GitHub account

The AI can create `content-queries/example.json`:
```json
{"tool":"query_alo_graph","arguments":{"type":"category","limit":25}}
```
The independent **AloTamiratchi MCP Site Context** workflow uses the private
MCP token and prints only public published context between markers
`ALO_PUBLIC_GRAPH_RESULT_START` and `ALO_PUBLIC_GRAPH_RESULT_END`. The
connected GitHub app can read this response directly from Actions logs. Draft
data is intentionally forbidden from public GitHub logs.

For content jobs in `content-requests/*.json`, `type` may be
`article`, `brand_article`, `brand` or `category`. `action` may be
`publish`, `draft`, `create`, `update` or `unpublish` as relevant.
Article publish preserves the existing idempotent slug workflow. Featured
media may be an allowlisted `image_url` or `image_base64`; never both.
Code deploy remains completely separate from both content queue directories.

## Prevent cPanel PHP handler loss

Never replace the live root .htaccess on cPanel. The deployed PHP handler is
often stored there. Previous one-time archives could overwrite it, leading
to PHP 5.x parse errors despite PHP 8.2 being configured in the account.
The protected package now supplies ALO-HTACCESS-RULES.txt for manual merging
instead, and automatic releases refuse to replace/delete .htaccess.
If a previous archive was extracted, re-select PHP 8.2 in MultiPHP Manager
for the exact domain and click Apply to restore its generated PHP handler.
