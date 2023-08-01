<?php

class SiteMap
{
    public function city()
    {
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
$dataHeader = $db->getLastInsert('header');
$dataFooter = $db->getLastInsert('footer');
        $get = $db->select('SELECT * FROM cities')->fetchAll();
        require_once BASE_PATH . '/mapcity/sitemap.php';
    }
    public function post(){
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
$dataHeader = $db->getLastInsert('header');
$dataFooter = $db->getLastInsert('footer');
        $get = $db->select('SELECT * FROM posts WHERE status = 1')->fetchAll();
        require_once BASE_PATH . '/mappost/sitemap.php';
    }
    public function brand()
    {
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
$dataHeader = $db->getLastInsert('header');
$dataFooter = $db->getLastInsert('footer');
        $get = $db->select('SELECT * FROM post_brand WHERE status = 1')->fetchAll();
        require_once BASE_PATH . '/mapbrand/sitemap.php';
    }
}