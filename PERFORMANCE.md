# Performance notes for shared hosting

The runtime rewrite removes repeated bootstrap work and repeated database configuration. Backend performance now depends mostly on MySQL indexes and the hosting provider's PHP OPcache.

Recommended indexes should be checked against the production schema before creation:

- users(user_name)
- users(email)
- cities(slug, status)
- posts(status, created_at)
- posts(category_id, status, created_at)
- posts(user_id, created_at)
- post_brand(status, created_at)
- post_brand(brand_id, status, updated_at)
- post_brand(user_id, created_at)
- view(post_id)
- view_brand(post_id)
- menu(sort)

Do not enable persistent PDO connections on shared hosting. The application deliberately keeps one normal lazy PDO connection per PHP request.

For the best result in the hosting panel, use PHP 8.2 or newer and enable OPcache if the provider offers a switch for it. No long-running server process is required.
