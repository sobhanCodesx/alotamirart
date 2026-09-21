<?php

class Brands
{
    public function index()
    {

    }

    public function category($id, $page)
    {
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        $prepage = 8;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        $brands = $db->select("SELECT * FROM post_brand WHERE brand_id = ? AND status = 1 ORDER BY updated_at DESC LIMIT {$start},{$prepage}", $id)->fetchAll();
        
        // ===== ✅ اصلاح: دریافت تعداد کل با نام ستون =====
        $countResult = $db->select("SELECT COUNT(`id`) as total FROM post_brand WHERE brand_id = ? AND status = 1", $id)->fetch();
        $totalPosts = isset($countResult['total']) ? $countResult['total'] : 0;
        $pages = ($totalPosts > 0) ? ceil($totalPosts / $prepage) : 1;
        
        $item = $db->new_select('*', 'items_brands', 'id', $id);
        require_once BASE_PATH . '/them/app/brands/categories.php';
    }

    public function show($id, $slug)
    {
        $err = null;
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $array = [$id , $slug];
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        
        // ===== اصلاح: استفاده از selectOne برای دریافت یک رکورد =====
        $post = $db->selectOne('SELECT *,(SELECT name FROM items_brands WHERE items_brands.id = post_brand.brand_id) AS namebrand FROM post_brand WHERE slug = ? AND id = ? AND status = 1', $array);
        
        if ($post) {
            $userId = isset($post['user_id']) ? $post['user_id'] : 0;
            if ($userId > 0) {
                $user = $db->select("SELECT * FROM users WHERE id = ?", $userId)->fetch();
            } else {
                $user = null;
            }

            if (isset($post['id']) && !empty($post['id'])) {
                $req['post_id'] = $post['id'];
                $db->insert("view_brand", array_keys($req), $req);
                $view = $db->select("SELECT COUNT(*) FROM view_brand WHERE post_id = ?", $post['id'])->fetchColumn();
            } else {
                $view = 0;
            }

            $err = true;
        }
        $side_brand = $db->select("SELECT * FROM post_brand WHERE status = 1 ORDER BY updated_at DESC LIMIT 0,6")->fetchAll();
        $side_post = $db->select("SELECT * FROM posts WHERE NOT id = ? AND status = 1 ORDER BY updated_at DESC LIMIT 0,8", $id)->fetchAll();
        require_once BASE_PATH . '/them/app/brands/post.php';
    }
}