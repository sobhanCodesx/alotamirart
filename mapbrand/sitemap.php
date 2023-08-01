<?php header('Content-type: application/xml;charset="utf-8"', true); ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">"
    <?php foreach ($get as $date) { ?>
        <url>
            <loc><?= assets($date['slug'].'/'.$date['id']) ?></loc>
            <lastmod><?= date('Y-m-d', time()); ?></lastmod>
            <priority>0.64</priority>
        </url>
    <?php } ?>
</urlset>