<?php

// ============================================================
// ===== مسیرهای عمومی سایت =====
// ============================================================

uri('/', 'Hom', 'index');
uri('/home', 'Hom', 'index');

// مقالات
uri('posts/categories/{id}/{page}', 'Posts', 'category');
uri('post/{id}', 'Posts', 'show');
uri('post/{id}/{slug}', 'Posts', 'show');

// شهرها
uri('cities', 'Cities', 'index');
uri('city/{slug}', 'Cities', 'show');
uri('services-in-{city}', 'Cities', 'services');

// سرویس در شهر
uri('refrigerator-repair-in-{city}', 'Cities', 'serviceDetail');
uri('washing-machine-repair-in-{city}', 'Cities', 'serviceDetail');
uri('air-conditioner-repair-in-{city}', 'Cities', 'serviceDetail');
uri('tv-repair-in-{city}', 'Cities', 'serviceDetail');
uri('oven-repair-in-{city}', 'Cities', 'serviceDetail');
uri('dishwasher-repair-in-{city}', 'Cities', 'serviceDetail');

// دسته برند
uri('brands/categories/{id}/{page}', 'Brands', 'category');

// سئو
uri('post/sitemap', 'SiteMap', 'post');
uri('brand/sitemap', 'SiteMap', 'brand');

// جستجو
uri('search/{page}', 'Hom', 'search', 'POST');

// احراز هویت
uri('register', 'Auth', 'register');
uri('registered', 'Auth', 'registered', 'POST');
uri('login', 'Auth', 'login');
uri('logined', 'Auth', 'logined', 'POST');
uri('logout', 'Auth', 'logout');

// پروفایل و منو
uri('profile/{name}/{id}', 'Hom', 'profile');
uri('menu/{slug}', 'Hom', 'menu');

// این route عمومی است و باید بعد از تمام مسیرهای مشخص دو بخشی باشد.
uri('{slug}/{id}', 'Brands', 'show');
