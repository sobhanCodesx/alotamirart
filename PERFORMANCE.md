# Performance plan for shared hosting

Performance work is compatibility-first. An optimization is not enabled when it can change business behavior without an equivalence test.

## Active safe optimizations

- One shared PDO connection is reused by all DataBase instances inside the same PHP request.
- Database credentials are loaded from one PHP config file instead of being duplicated.
- No persistent PDO connection is used.
- The project remains compatible with ordinary shared PHP hosting.

## Staged optimizations

The newer lightweight router, file cache, service layer and controller architecture remain available as migration code but are not the production request path yet.

They should be activated feature by feature only after compatibility tests cover the relevant legacy behavior.

Database indexes, response caching, query rewrites and authentication migrations must be reviewed against the real production schema/data before activation.
