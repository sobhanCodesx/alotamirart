<?php


uri('panelcp','Panel','indexPanel');

/////////////////////////////////////////////////////
uri('user/post/{page}','PostUser','index');
uri('user/posts/create','PostUser','create');
uri('user/post/created','PostUser','created','POST');
uri('user/post/update/{id}','PostUser','update');
uri('userpost/updated/{id}','PostUser','updated',"POST");


///////////// panel brand ////////////////////////////

uri('user/brand/{page}','BrandUser','index');
uri('userbrand/create','BrandUser','create');
uri('user/brand/created','BrandUser','created','POST');
uri('user/brand/update/{id}','BrandUser','update');
uri('user/brand/update/{id}','BrandUser','updated');
uri('userbrand/updated/{id}','BrandUser','updated','POST');
