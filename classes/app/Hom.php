<?php

class Hom
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    // ===== صفحه اصلی =====
    public function index()
    {
        $dataSeo = $this->db->getLastInsert('seo');
        $dataHeader = $this->db->getLastInsert('header');
        $dataFooter = $this->db->getLastInsert('footer');
        $menu = $this->db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        $brand = $this->db->select('SELECT * FROM items_brands LIMIT 0,12')->fetchAll();
        
        // ===== دریافت ۸ مقاله آخر =====
        $post = $this->db->select('SELECT * FROM posts WHERE status = 1 ORDER BY created_at DESC LIMIT 0,8')->fetchAll();
        $brands = $this->db->select('SELECT * FROM post_brand WHERE status = 1 ORDER BY created_at DESC LIMIT 0,8')->fetchAll();
        
        require_once BASE_PATH . '/them/app/index.php';
    }

    // ===== منوها و دسته‌بندی‌ها =====
    public function menu($slug)
    {
        $dataSeo = $this->db->getLastInsert('seo');
        $dataHeader = $this->db->getLastInsert('header');
        $dataFooter = $this->db->getLastInsert('footer');
        $menu = $this->db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();

        $category = $this->db->new_select('*', 'menu', 'slug', $slug);
        if (!$category) {
            http_response_code(404);
            require BASE_PATH . '/404.php';
            return;
        }

        $posts = $this->db->select(
            "SELECT * FROM posts WHERE post_id = ? AND status = 1 ORDER BY created_at DESC",
            $category['id']
        )->fetchAll();

        $post = $posts;
        $item = $category;
        $page = 1;
        $pages = 1;
        require BASE_PATH . '/them/app/posts/categories.php';
    }
}
