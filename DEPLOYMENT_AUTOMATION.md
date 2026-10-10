# Deployment automation for AloTamirArt (bootstrap required)

This implementation follows the high-level **game-shop** deployment pattern: changes from GitHub `main` pass CI, and a signed HTTPS deployment agent receives and applies an allowlisted package on a shared PHP/Apache host without SSH.

## Current status

The signed `api/deploy.php` receiver is already installed and authenticated on both `alotamiratchi.ir` and `www.alotamiratchi.ir`. GitHub confirmed unauthenticated POST = HTTP 401 and an authenticated, intentionally invalid ZIP = HTTP 422. However, BitNinja returns HTTP 403 when the valid signed archive contains real PHP source. The newer MCP code in `main` is **not yet confirmed deployed**. A verified-TLS FTP fallback is now available but needs one-time private GitHub Secrets, after which pending MCP code can be sent by GitHub without manual file uploads.

## Security prerequisites — MUST complete first

1. **Rotate exposed DB passwords** currently present in tracked `config/database.php` and move credentials into protected server-only configuration. Git history already contains secrets; merely editing the file is insufficient. Review the tracked `error_log` for any sensitive data.
2. Make a full production file and database backup from the host panel and verify restore access.
3. Confirm hosting supports PHP 8.1+, PDO MySQL, mbstring and ZipArchive. Ensure `sys_get_temp_dir()` is writable and `MCP_API_TOKEN` can be read through `getenv()`.
4. Configure `MCP_API_TOKEN` as a private, strong random root secret at the hosting environment; use the identical value as a GitHub Actions secret of the same name. Never put secrets in the repo or share them in chat. The deployment endpoint derives a separate deployment bearer and package-signing HMAC from the root.
5. Set `ALO_DEPLOY_ENABLED=1` in the hosting environment only when ready. Never expose a deployment endpoint without HTTPS and network rate limits/WAF.
6. Set GitHub Actions secret `ALO_DEPLOY_URL=https://YOUR-HOST/api/deploy.php`.
7. Optionally require reviewers in the GitHub `production` environment and protect the `main` branch; require passing checks before merge.

## One-time bootstrap

Existing PHP hosting cannot receive signed deploy requests until the receiver file has been installed **once**. Upload `api/deploy.php` manually to the existing document root, preserving paths. Do **not** upload a ZIP across the whole live site or replace the database config. Check it returns HTTP 405 to an unauthenticated GET and HTTP 401 to an unauthenticated POST. Enable hosting environment variables **after** credential rotation and testing.

Once the new PR passes CI and is merged to `main`, `.github/workflows/deploy-alo-production.yml` runs on future pushes to `main`. If GitHub secrets or the endpoint are not configured, deployment will fail closed rather than silently succeeding. Manual `workflow_dispatch` can retry the latest commit's diff (from its immediate parent).

## Deployment safety model

- Only code from `refs/heads/main` can be uploaded via the GitHub sender.
- GitHub uses its committed exact commit SHA; package is signed with HMAC SHA-256 and sent over HTTPS.
- Server verifies derived bearer token, signature and allowed file extensions/paths; disallows traversals, symlink ZIP entries, secrets, logs and user uploads. Database migrations and file deletions are NOT automated.
- Existing affected files are copied to a temporary backup, then staged files are installed; on an installation error the receiver attempts to restore previously changed files.
- Concurrent deployments are refused using `flock`, and package size/file count are capped.
- Keep a manual file/database restore path. **Rollback is best-effort**, not transactional or guaranteed across process crashes/power failures. There is no automatic DB rollback. This is a lightweight receiver, not full parity with game-shop's mature deployment manager.
- This v1 deploys changed files only. A server that has drifted from GitHub may still contain unrelated local files. Before first production use, compare live files with repo, verify permissions, and test on staging. Do not use for a full initial synchronization.
- Changes to root `index.php`, `.htaccess`, root-level PHP entry files, tracked database credential files and paths outside the allowlist are intentionally excluded from automatic deployment. They require separately reviewed/manual installation.
- If production responds with a non-JSON error or a server-side 5xx, review host error logs and recover from the last known backup. Do not retry blindly.

## Deployment trigger and result

A merge or push to `main` triggers the workflow. The user can ask ChatGPT in this conversation to edit code and prepare a PR; with the connected GitHub app, after explicit authorization, ChatGPT can merge the approved PR, triggering GitHub Actions. It can inspect workflow status afterwards. **The GitHub app by itself cannot upload files to private hosting or make the custom MCP tool available in ChatGPT.** That depends on installing this receiver and separately registering the MCP HTTPS endpoint in a supported ChatGPT environment.

## Test commands in CI

```sh
php -l api/deploy.php
php tests/deploy-contract.php
python3 -m py_compile scripts/alo_deploy.py
```

**No production deploy has been initiated by this PR.**

## Automatic fallback via FTPS (no second manual code upload)

GitHub Actions first attempts the HMAC-signed HTTPS deployment receiver. If BitNinja blocks a legitimate PHP ZIP with HTTP 403 and a recognized blocking page, the deploy sender automatically uses **explicit FTPS with TLS certificate verification** instead.

Configure these values privately under **GitHub repository → Settings → Secrets and variables → Actions → Repository secrets**:

- `ALO_FTPS_HOST` — the hosting FTP server name from cPanel **Configure FTP Client**, without `ftp://`
- `ALO_FTPS_USERNAME` — the full username of a dedicated FTP account restricted to this website
- `ALO_FTPS_PASSWORD` — that FTP account's private password

Optional GitHub Actions repository **variable**: `ALO_FTPS_ROOT`. When omitted, the sender tests the FTP account root, `public_html`, and `www`; it refuses to upload until it verifies both the existing `index.php` and the AloTamirArt `api/deploy.php` receiver.

Use explicit TLS/FTPS on port 21 and a host name with a matching valid certificate. No secrets belong in the repository or in a chat. The sender only deploys changed, allowlisted application paths from `main`; it cannot upload `.env`, local database credentials, user uploads, images stored on the host, caches, or log files. A temporary/backup file for PHP keeps the `.php` extension so the web server does not expose PHP source code.

**Important:** Successful GitHub syntax checks do not establish live FTPS connectivity; a real deployment must be attempted after secrets are configured. Since older MCP application changes failed to deploy over HTTPS, they also require a one-time **GitHub-triggered catch-up deployment** with a reviewed baseline before normal incremental pushes resume. The catch-up must be returned to regular `github.event.before` tracking immediately after successful installation.

Publishing articles via MCP is independent of this code deployment process and does not trigger a new site deployment.
