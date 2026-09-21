<?php

class PostUser extends Panel
{
    public function index($page)
    {
        $db = new \DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        
        $prepage = 10;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
        
        // ===== دریافت پست‌های کاربر =====
        $post = $db->select("SELECT * FROM posts WHERE user_id = ? ORDER BY id DESC LIMIT {$start},{$prepage}", $userId)->fetchAll();
        
        // ===== ✅ اصلاح: دریافت تعداد کل پست‌ها =====
        $countResult = $db->select("SELECT COUNT(`id`) as total FROM posts WHERE user_id = ?", $userId)->fetch();
        $totalPosts = isset($countResult['total']) ? $countResult['total'] : 0;
        $pages = ($totalPosts > 0) ? ceil($totalPosts / $prepage) : 1;
        
        if ($page > $pages && $pages > 0) {
            $this->redirecte('user/post/1');
            return;
        }
        
        require_once BASE_PATH . '/them/panel/posts/index.php';
    }

    public function create()
    {
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        require_once BASE_PATH . '/them/panel/posts/create.php';
    }

    public function created($req)
    {
        $db = new \DataBase();

        if (isset($req['img']['tmp_name']) && $req['img']['tmp_name'] != null && $req['img']['error'] == 0) {
            $savedImage = $this->saveImage($req['img'], 'img-');
            if ($savedImage) {
                $req['img'] = $savedImage;
            } else {
                unset($req['img']);
            }
        } else {
            unset($req['img']);
        }

        $req['user_id'] = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

        if (isset($req['title']) && !empty($req['title'])) {
            $req['slug'] = $this->createSlug($req['title']);
        }

        $db->insert('posts', array_keys($req), $req);
        flash('success', 'پست با موفقیت ایجاد شد');
        $this->redirecte('user/post/1');
    }

    public function update($id)
    {
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $post = $db->new_select('*', 'posts', 'id', $id);
        
        if (!$post) {
            flash('error', 'پست یافت نشد');
            $this->redirecte('user/post/1');
            return;
        }
        
        require_once BASE_PATH . '/them/panel/posts/update.php';
    }

    public function updated($req, $id)
    {
        $db = new DataBase();

        $currentPost = $db->selectOne("SELECT * FROM posts WHERE id = ?", [$id]);

        if (!$currentPost) {
            flash('error', 'پست یافت نشد');
            $this->redirecte('user/post/1');
            return;
        }

        if (empty($req['title']) || empty($req['content'])) {
            flash('error', 'عنوان و محتوا الزامی هستند');
            $this->redirecte('user/post/1');
            return;
        }

        $data = [
            'title' => $req['title'],
            'description' => isset($req['description']) ? $req['description'] : (isset($currentPost['description']) ? $currentPost['description'] : ''),
            'content' => $req['content'],
            'tags' => isset($req['tags']) ? $req['tags'] : (isset($currentPost['tags']) ? $currentPost['tags'] : ''),
            'post_id' => isset($req['post_id']) ? $req['post_id'] : (isset($currentPost['post_id']) ? $currentPost['post_id'] : null),
            'status' => isset($req['status']) ? $req['status'] : $currentPost['status'],
            'user_id' => isset($currentPost['user_id']) ? $currentPost['user_id'] : (isset($_SESSION['id']) ? $_SESSION['id'] : 0)
        ];

        if (isset($req['title']) && !empty($req['title'])) {
            $data['slug'] = $this->createSlug($req['title']);
        }

        if (isset($req['img']['tmp_name']) && $req['img']['tmp_name'] != null && $req['img']['error'] == 0) {
            if (!empty($currentPost['img'])) {
                $this->removeImage($currentPost['img']);
            }
            
            $savedImage = $this->saveImage($req['img'], 'img-');
            if ($savedImage) {
                $data['img'] = $savedImage;
            }
        } else {
            if (isset($currentPost['img']) && !empty($currentPost['img'])) {
                $data['img'] = $currentPost['img'];
            }
        }

        $result = $db->update('posts', $id, array_keys($data), array_values($data));

        if ($result) {
            flash('success', 'پست با موفقیت به‌روزرسانی شد');
        } else {
            flash('error', 'خطا در به‌روزرسانی پست');
        }

        $this->redirecte('user/post/1');
    }

    public function delete($id)
    {
        $db = new DataBase();
        $post = $db->new_select('*', 'posts', 'id', $id);
        
        if ($post && !empty($post['img'])) {
            $this->removeImage($post['img']);
        }
        
        $db->delete('posts', $id);
        flash('success', 'پست با موفقیت حذف شد');
        $this->redirecte('user/post/1');
    }

    protected function createSlug($title)
    {
        $slug = trim($title);
        $slug = str_replace(' ', '-', $slug);
        $slug = preg_replace('/[^a-zA-Z0-9\-]/', '', $slug);
        $slug = strtolower($slug);
        return $slug;
    }
}