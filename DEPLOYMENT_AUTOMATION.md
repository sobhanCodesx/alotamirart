# AloTamiratchi deployment — PlayNexus architecture on PHP 8.2/cPanel

This implementation follows the source of `sobhanCodesx/game-shop`, adapted to the existing PHP 8.2/MySQL site. It does **not** use FTP, SFTP, SSH, Laravel, Composer, or the old single-request ZIP sender.

## Same deployment lifecycle as PlayNexus

1. GitHub `main` code change (NOT `content-requests/*.json`) triggers `.github/workflows/deploy-alo-production.yml`.
2. PHP syntax and deployment/security contract checks must pass.
3. `scripts/alo_deploy_playnexus.py` signs a manifest listing the exact commit SHA, allowed file paths and per-file SHA-256 digests. It creates a ZIP of the changed application code.
4. GitHub uploads via `multipart/form-data` in 512 KiB chunks, and receives an operation id.
5. The new receiver completes the upload, verifies the signed manifest, files and allowlist, then applies changes in distinct backup, switch and health-check stages. Failed switches restore file backups.
6. GitHub confirms the final deployment status and matching commit SHA via authenticated health check, then probes the public homepage.

### PHP shared-host endpoint

In lieu of PlayNexus's Laravel router, PHP's DirectoryIndex serves the same stages at:

`/api/deployment-agent/?action=upload/chunk`
`/api/deployment-agent/?action=upload/complete`
`/api/deployment-agent/?action={id}/verify`
`/api/deployment-agent/?action={id}/apply`
`/api/deployment-agent/?action={id}/status`
`/api/deployment-agent/?action=health`

The receiver is `api/deployment-agent/index.php`. The deployment Bearer secret and manifest signing key are HMAC-derived from the existing private `MCP_API_TOKEN`; credentials are never stored in Git or printed. Files go to a private temporary directory; paths containing secrets, uploads, symlinks and database configuration are rejected. The workflow does not deploy for MCP article updates.

### Initial receiver installation remains necessary

The **old** `api/deploy.php` receiver is already live and its signed authentication works. It only accepts a monolithic raw ZIP, however, and BitNinja returns HTTP 403 for ZIPs containing PHP application files, even when the signature is correct. The new staged receiver is not yet live and cannot be invoked until its PHP file is installed.

A new GitHub commit **cannot by itself bootstrap this new HTTP endpoint** through a legacy receiver blocked by the hosting firewall. A one-time scoped host permission for the signed old receiver, or installation of `api/deployment-agent/index.php` using an authorized hosting operation, is required to bridge that gap. Do **not** disable site-wide security protections. Once installed, all future code releases use the PlayNexus-style multipart workflow with no manual uploads.

For the first catch-up release, GitHub Actions variable `ALO_DEPLOY_BASE_SHA` can temporarily designate the reviewed SHA prior to the failed legacy deployments. Remove that variable after successful catch-up so later commits only deploy their own code changes.

## Tests

`.github/workflows/playnexus-deployment-contract.yml` runs an integration test against a real local PHP 8.2 HTTP server, covering auth, chunk upload, manifest verification, backup, apply, final status, signed health check and public smoke check. This **does not** prove the receiving endpoint is already installed on the live domain.

The old `api/deploy.php` and old sender remain in the repository solely for compatibility/bootstrap; the new production workflow does not use FTP/FTPS or the old monolithic ZIP transfer.
