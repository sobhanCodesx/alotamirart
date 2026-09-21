<?php

class Hom
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    private function shared()
    {
        return [
            'dataSeo' => $this->db->getLastInsert('seo'),
            'dataHeader' => $this->db->getLastInsert('header'),
            'dataFooter' => $this->db->getLastInsert('footer'),
            'menu' => $this->db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll(),
        ];
    }

    public function index()
    {
        extract($this->shared());
        $brand = $this->db->select('SELECT * FROM items_brands LIMIT 0,12')->fetchAll();
        $post = $this->db->select('SELECT * FROM posts WHERE status = 1 ORDER BY created_at DESC LIMIT 0,8')->fetchAll();
        $brands = $this->db->select('SELECT * FROM post_brand WHERE status = 1 ORDER BY created_at DESC LIMIT 0,8')->fetchAll();
        require BASE_PATH . '/them/app/index.php';
    }

    public function menu($slug)
    {
        extract($this->shared());
        $category = $this->db->new_select('*', 'menu', 'slug', $slug);

        if (!$category) {
            http_response_code(404);
            require BASE_PATH . '/404.php';
            return;
        }

        $post = $this->db->select(
            "SELECT * FROM posts WHERE post_id = ? AND status = 1 ORDER BY created_at DESC",
            $category['id']
        )->fetchAll();
        $item = $category;
        $page = 1;
        $pages = 1;

        require BASE_PATH . '/them/app/posts/categories.php';
    }

    public function search($req, $page)
    {
        extract($this->shared());

        $query = isset($req['search']) ? trim((string)$req['search']) : '';
        $page = max(1, (int)$page);
        $prepage = 10;
        $start = ($page - 1) * $prepage;
        $err = false;
        $post = [];
        $pages = 1;

        if ($query !== '') {
            $like = '%' . $query . '%';
            $post = $this->db->select(
                "SELECT * FROM post_brand WHERE title LIKE ? AND status = 1 ORDER BY created_at DESC LIMIT {$start},{$prepage}",
                [$like]
            )->fetchAll();

            $countResult = $this->db->select(
                "SELECT COUNT(id) AS total FROM post_brand WHERE title LIKE ? AND status = 1",
                [$like]
            )->fetch();

            $totalPosts = isset($countResult['total']) ? (int)$countResult['total'] : 0;
            $pages = max(1, (int)ceil($totalPosts / $prepage));
            $err = !empty($post);
        }

        $result = $query;
        require BASE_PATH . '/them/app/search.php';
    }

    public function profile($name, $id)
    {
        extract($this->shared());

        $id = (int)$id;
        $name = (string)$name;
        $user = $this->db->selectOne(
            'SELECT * FROM users WHERE user_name = ? AND id = ?',
            [$name, $id]
        );

        if (!$user) {
            http_response_code(404);
            require BASE_PATH . '/404.php';
            return;
        }

        $posts = $this->db->select(
            'SELECT id,title,user_id,description,content,img FROM posts WHERE user_id = ? AND status = 1 ORDER BY created_at DESC LIMIT 0,6',
            [$id]
        )->fetchAll();

        $brand = $this->db->select(
            'SELECT id,title,content,brand_id,des,img,slug FROM post_brand WHERE user_id = ? AND status = 1 ORDER BY created_at DESC LIMIT 0,6',
            [$id]
        )->fetchAll();

        foreach ($posts as $key => $post) {
            $posts[$key]['view'] = (int)$this->db->select(
                'SELECT COUNT(id) FROM view WHERE post_id = ?',
                [$post['id']]
            )->fetchColumn();
        }

        foreach ($brand as $key => $brandPost) {
            $brand[$key]['view'] = (int)$this->db->select(
                'SELECT COUNT(id) FROM view_brand WHERE post_id = ?',
                [$brandPost['id']]
            )->fetchColumn();
        }

        if (empty($user['img'])) {
            $user['img'] = 'them/admin/dist/img/avatar.png';
        }

        require BASE_PATH . '/them/app/profile/index.php';
    }
}
