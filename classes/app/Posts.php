<?php

class Posts
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function category($id, $page)
    {
        $dataSeo = $this->db->getLastInsert('seo');
        $dataHeader = $this->db->getLastInsert('header');
        $dataFooter = $this->db->getLastInsert('footer');
        $menu = $this->db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        
        $prepage = 8;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        
        // ===== ✅ اصلاح: اسم ستون رو با توجه به دیتابیس خودت عوض کن =====
        // اگر اسم ستون post_id هست:
        $posts = $this->db->select("SELECT * FROM posts WHERE post_id = ? AND status = 1 ORDER BY created_at DESC LIMIT {$start},{$prepage}", $id)->fetchAll();
        $countResult = $this->db->select("SELECT COUNT(`id`) as total FROM posts WHERE post_id = ? AND status = 1", $id)->fetch();
        
        // اگر اسم ستون menu_id هست:
        // $posts = $this->db->select("SELECT * FROM posts WHERE menu_id = ? AND status = 1 ORDER BY created_at DESC LIMIT {$start},{$prepage}", $id)->fetchAll();
        // $countResult = $this->db->select("SELECT COUNT(`id`) as total FROM posts WHERE menu_id = ? AND status = 1", $id)->fetch();
        
        $totalPosts = isset($countResult['total']) ? $countResult['total'] : 0;
        $pages = ($totalPosts > 0) ? ceil($totalPosts / $prepage) : 1;
        
        $category = $this->db->new_select('*', 'menu', 'id', $id);
        
        $post = $posts;
        $item = $category;
        $page = $page;
        $pages = $pages;
        
        require_once BASE_PATH . "/them/app/posts/categories.php";
    }

    public function show($id, $slug = null)
    {
        $err = null;
        $dataSeo = $this->db->getLastInsert('seo');
        $dataHeader = $this->db->getLastInsert('header');
        $dataFooter = $this->db->getLastInsert('footer');
        $menu = $this->db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        
        $post = $this->db->select('SELECT * FROM posts WHERE id = ? AND status = 1', $id)->fetch();
        
        if ($post) {
            $user = $this->db->select('SELECT * FROM users WHERE id = ?', $post['user_id'])->fetch();
            $req['post_id'] = $post['id'];
            $this->db->insert("view", array_keys($req), $req);
            $view = $this->db->select("SELECT COUNT(*) FROM view WHERE post_id = ?", $post['id'])->fetchColumn();
            $err = true;
        }
        
        $side_brand = $this->db->select("SELECT * FROM post_brand WHERE status = 1 ORDER BY created_at DESC LIMIT 0,6")->fetchAll();
        $side_post = $this->db->select("SELECT * FROM posts WHERE NOT id = ? AND status = 1 ORDER BY created_at DESC LIMIT 0,8", $id)->fetchAll();
        require_once BASE_PATH . "/them/app/posts/post.php";
    }
}