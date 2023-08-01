<?php
uri('/', 'Hom', 'index');
uri('/home', 'Hom', 'index');
uri('post/sitemap', 'SiteMap', 'post');
uri('brand/sitemap', 'SiteMap', 'brand');
uri('brands/categories/{id}/{page}', 'Brands', 'category');
uri('posts/categories/{id}/{page}', 'Posts', 'category');
uri('post/{id}', 'Posts', 'show');
uri('{slug}/{id}', 'Brands', 'show');
uri('search/{page}', 'Hom', 'search', "POST");
uri('register', 'Auth', 'register');
uri('registered', 'Auth', 'registered', 'POST');
uri('login', 'Auth', 'login');
uri('logined', 'Auth', 'logined', 'POST');
uri('logout', 'Auth', 'logout');

