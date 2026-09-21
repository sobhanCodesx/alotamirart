# AloTamirArt architecture

## Compatibility-first rule

The production runtime deliberately preserves the behavior that existed before the architecture refactor. The compatibility baseline is commit ab233f8e83150a1ea7f4c3f09de187f782edfa70.

No legacy route, form contract, authentication behavior, database schema, or public URL should be replaced only for architectural cleanliness. New components must be introduced behind compatibility boundaries and activated only after tests prove equivalent behavior.

## Active production request flow

Apache .htaccess -> index.php -> legacy router files -> existing classes -> existing views.

This is intentional. The newer app/Core, app/Http, app/Services, bootstrap and routes directories are a migration target, not the active production request path yet.

## Safe improvements currently active

- Database connection settings are centralized in config/database.php.
- Historical DB constants are preserved separately in that same file to avoid an accidental compatibility break.
- DataBase keeps its historical public API and global instance.
- Multiple DataBase objects in one PHP request reuse one PDO connection.
- Standalone city/service pages keep their original HTML, SQL and URL behavior while reading credentials from the central config.
- The original Apache rewrite rules remain active.
- Compatibility checks run in CI before future migration work is merged.

## Migration strategy

1. Freeze the legacy behavior as a contract.
2. Add a replacement component without changing the active route.
3. Add equivalence tests for inputs, outputs and side effects.
4. Switch one bounded feature at a time.
5. Keep rollback possible through ordinary Git commits.
6. Remove old code only after the replacement has been proven in production.

## Shared hosting

The active runtime requires only normal PHP, Apache rewrite support and MySQL. No Redis, daemon, queue worker, Docker, supervisor, persistent process or server-level service is required.
