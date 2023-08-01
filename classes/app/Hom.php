<?php

class Hom
{
    public function index()
    {
        $db = new DataBase();
        unsetUsers();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        $brand = $db->select('SELECT * FROM items_brands')->fetchAll();
        $post = $db->select('SELECT * FROM posts WHERE status = 1 ORDER BY created_at DESC LIMIT 0,8')->fetchAll();
        $brand = $db->select('SELECT * FROM items_brands  LIMIT 0,12')->fetchAll();
        $brands = $db->select('SELECT * FROM post_brand WHERE status = 1 ORDER BY created_at DESC LIMIT 0,8')->fetchAll();
        require_once BASE_PATH . '/them/app/index.php';
    }

    public function search($req, $page)
    {
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $err = null;
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        $result = $req['search'];
        $prepage = 10;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        $post = $db->select("SELECT * FROM post_brand WHERE title LIKE '%$result%' AND status = 1 ORDER BY created_at DESC LIMIT {$start},{$prepage}")->fetchAll();
        $count = $db->select("SELECT COUNT(`id`) FROM post_brand")->fetch();
        $pages = ceil($count[0] / $prepage);
        if ($post) {
            $err = true;
        }
        require_once BASE_PATH . '/them/app/search.php';
    }
}