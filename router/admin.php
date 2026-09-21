<?php
// primery page admin
uri('admin/dashboard', 'Dashboard', 'index');
uri('admin/cities/{id}/{page}', 'Cities', 'index');
uri('admin/create/cities/{id}', 'Cities', 'create', "POST");
uri('admin/update/cities/{id}', 'Cities', 'updated', "POST");
uri('admin/cities/delete/{id}/{provi}', 'Cities', 'deletd');
////////////// routing menu or cate gories//
uri('admin/menu/index/{page}', 'Menu', 'index');
uri('admin/menu/create', 'Menu', 'create');
uri('admin/menu/created', 'Menu', 'created', 'POST');
uri('admin/menu/update/{id}', 'Menu', 'update');
uri('admin/menu/updated/{id}', 'Menu', 'updated', 'POST');
uri('admin/menu/deleted/{id}', 'Menu', 'deleted');
///////////////////// routing posts ///////////////////
uri('admin/posts/index/{page}', 'Post', 'index');
uri('admin/posts/create', 'Post', 'create');
uri('admin/posts/created', 'Post', 'created', 'POST');
uri('admin/posts/update/{id}', 'Post', 'update');
uri('admin/posts/updated/{id}', 'Post', 'updated', 'POST');
uri('admin/posts/deleted/{id}', 'Post', 'deleted');
uri('change/status/{id}','Post','status');
////////////////////// routing users ///////////////////////////////////////
uri('admin/users/{page}', 'User', 'index');
uri('admin/users/status/{id}', 'User', 'status');
uri('admin/users/delete/{id}','User','delete');
uri('user/status/{id}','User','writer');
uri('inner/user/{id}','User','innerUser');
//////////////////// routing brand items ////////////////////////////////////////
uri('admin/brands/index/{page}','Items','index');
uri('admin/brands/created','Items','created',"POST");
uri('admin/brands/update/{id}','Items','update');
uri('admin/brands/updated/{id}','Items','updated','POST');
uri('admin/brands/delete/{id}','Items','deleted');
//////////////////////// routing brand posts ///////////////////////////////////////////
uri('admin/brands/post/{page}','BrandPosts','index');
uri('admin/brands/blog/create','BrandPosts','create');
uri('admin/brands/blogs/created','BrandPosts','created',"POST");
uri('admin/brands/post/update/{id}','BrandPosts','update');
uri('admin/brands/post/updated/{id}','BrandPosts','updated','POST');
uri('admin/brands/post/delete/{id}','BrandPosts','deleted');
uri('status/brand/{id}','BrandPosts','status');
////////// back link
uri("backlink/admin/{page}","Link","index");
uri("backlink/admin/create","Link","create","POST");
uri("delete/backlink/{id}","Link","delete");
////////////////////////////////////////////////////////////////////////////
uri('admin/settings/seo','Seo','index');
uri('admin/settings/seo/create','Seo','create',"POST");
uri('admin/settings/header','Header','index');
uri('admin/settings/header/create','Header','create',"POST");
uri('admin/settings/footer','Footer','index');
uri('admin/settings/footer/create','Footer','create',"POST");

/////////////////////////////////////////////////////////////////