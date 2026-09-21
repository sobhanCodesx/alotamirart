# AloTamirArt architecture

This application is intentionally built for shared PHP hosting: no daemon, no queue worker, no Redis requirement, no framework bootstrap overhead, and no Composer requirement at runtime.

## Request flow

Apache htaccess -> root index.php -> bootstrap/app.php -> lightweight Router -> controller -> Database/services -> existing PHP views.

The document root stays at the repository root because many shared-hosting panels do not allow changing it. Internal directories are denied by htaccess.

## Directories

- app/Core: request lifecycle, router, PDO connection, view renderer, cache, logging and container.
- app/Services: shared site context, authentication and uploads.
- app/Http/Controllers: public website controllers.
- app/Http/Admin: admin controllers.
- app/Http/Panel: writer/user panel controllers.
- routes: route definitions only.
- config: editable application/database configuration.
- storage/cache: filesystem cache suitable for shared hosting.
- storage/logs: application error logs.
- them: existing views/assets kept for business compatibility.

## Performance decisions

- One lazy PDO connection per request.
- Native prepared statements with utf8mb4.
- No persistent PDO connections, which are often harmful on shared hosting.
- Common SEO/header/footer/menu data is memoized in-request and cached on disk.
- Admin changes invalidate the relevant shared cache.
- Static assets receive browser-cache headers and compression when Apache modules are available.
- The router and autoloader are small dependency-free PHP code that benefits from host OPcache automatically.
- Profile view counts are fetched without N+1 PHP query loops.

## Compatibility

Existing public URLs, admin URLs and writer-panel URLs are retained. Existing views are reused so the rewrite changes the execution architecture without replacing the site's visual/business layer in one risky deployment.

Legacy plaintext passwords are accepted once and transparently migrated to password_hash on successful login.

## Shared-hosting deployment

Set the real database values in config/database.php, or host environment variables if supported. Ensure storage/cache, storage/logs, and upload directories are writable by PHP. PHP 7.4+ is required; PHP 8.1+ is recommended.
