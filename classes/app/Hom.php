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
            echo "دسته‌بندی مورد نظر یافت نشد";
            return;
        }
        
        $posts = $this->db->select("SELECT * FROM posts WHERE category_id = ? AND status = 1 ORDER BY created_at DESC", $category['id'])->fetchAll();
        
        require_once BASE_PATH . '/them/app/menu.php';
    }

    // ===== جستجو =====
    public function search($req, $page)
    {
        $dataSeo = $this->db->getLastInsert('seo');
        $dataHeader = $this->db->getLastInsert('header');
        $dataFooter = $this->db->getLastInsert('footer');
        $err = null;
        $menu = $this->db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        $result = $req['search'];
        $prepage = 10;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        
        $post = $this->db->select("SELECT * FROM post_brand WHERE title LIKE '%$result%' AND status = 1 ORDER BY created_at DESC LIMIT {$start},{$prepage}")->fetchAll();
        $countResult = $this->db->select("SELECT COUNT(`id`) as total FROM post_brand")->fetch();
        $totalPosts = isset($countResult['total']) ? $countResult['total'] : 0;
        $pages = ($totalPosts > 0) ? ceil($totalPosts / $prepage) : 1;
        
        if ($post) {
            $err = true;
        }
        require_once BASE_PATH . '/them/app/search.php';
    }

public function profile($name, $id)
{
    $db = new DataBase();
    $aReqLog = [$name, $id];
    $user = $db->all("SELECT * FROM users WHERE user_name = ? AND id = ?", $aReqLog);
    $dataSeo = $db->getLastInsert('seo');
    $dataHeader = $db->getLastInsert('header');
    $dataFooter = $db->getLastInsert('footer');
    $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
    $posts = $db->select('SELECT id,title,user_id,description,content,img FROM posts WHERE user_id = ? AND status = 1 ORDER BY created_at DESC LIMIT 0,6', $id)->fetchAll();
    $brand = $db->select('SELECT id,title,content,brand_id,des,img,slug FROM post_brand WHERE user_id = ? AND status = 1 ORDER BY created_at DESC LIMIT 0,6', $id)->fetchAll();

    foreach ($posts as $key => $post) {
        $posts[$key]['view'] = $db->select("SELECT COUNT(id) FROM view WHERE post_id = ?", $post['id'])->fetchColumn();
    }
    foreach ($brand as $key => $post) {
        $brand[$key]['view'] = $db->select("SELECT COUNT(id) FROM view_brand WHERE post_id = ?", $post['id'])->fetchColumn();
    }

    if (empty($user['id'])) {
        header("location: " . assets('/'));
    }

    if (empty($user['img'])) {
        $user['img'] = 'them/admin/dist/img/avatar.png';
    }
    
    // ===== متغیرها رو به ویو ارسال کن =====
    $posts = $posts;
    $brand = $brand;
    $user = $user;
    
    require_once BASE_PATH . '/them/app/profile/index.php';
}}