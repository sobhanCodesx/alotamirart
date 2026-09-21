<?php

class Post extends Admin
{
    public function index($page)
    {
        $db = new \DataBase();
      
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        
        $prepage = 20;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        
        // ===== دریافت مقالات =====
        $post = $db->select("SELECT *,(SELECT name FROM users WHERE users.id = posts.user_id) AS w FROM posts ORDER BY created_at DESC LIMIT {$start},{$prepage}")->fetchAll();
        
        // ===== دریافت تعداد کل مقالات =====
        $countResult = $db->select("SELECT COUNT(`id`) as total FROM posts")->fetch();
        $totalPosts = isset($countResult['total']) ? $countResult['total'] : 0;
        $pages = ($totalPosts > 0) ? ceil($totalPosts / $prepage) : 1;
        
        // ===== دیباگ: تعداد مقالات رو چاپ کن =====
        // echo "تعداد مقالات: " . count($post);
        
        require_once BASE_PATH . '/them/admin/pages/posts/index.php';
    }

    // ===== سایر متدها =====
    public function create()
    {
        $db = new \DataBase();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        require_once BASE_PATH . '/them/admin/pages/posts/create.php';
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

        if (!isset($req['contact_number'])) {
            $req['contact_number'] = null;
        }

        if (isset($req['title']) && !empty($req['title'])) {
            $req['slug'] = $this->createSlug($req['title']);
        }

        $db->insert('posts', array_keys($req), $req);
        flash('success', 'پست با موفقیت ایجاد شد');
        $this->redirect('admin/posts/index/1');
    }

    public function update($id)
    {
        $db = new \DataBase();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $post = $db->new_select('*', 'posts', 'id', $id);
        
        if (!$post) {
            flash('error', 'پست یافت نشد');
            $this->redirect('admin/posts/index/1');
            return;
        }
        
        require_once BASE_PATH . '/them/admin/pages/posts/update.php';
    }

    public function updated($req, $id)
    {
        $db = new \DataBase();

        $currentPost = $db->new_select('*', 'posts', 'id', $id);

        if (isset($req['img']['tmp_name']) && $req['img']['tmp_name'] != null && $req['img']['error'] == 0) {
            if ($currentPost && !empty($currentPost['img'])) {
                $this->removeImage($currentPost['img']);
            }
            
            $savedImage = $this->saveImage($req['img'], 'img-');
            if ($savedImage) {
                $req['img'] = $savedImage;
            } else {
                unset($req['img']);
            }
        } else {
            unset($req['img']);
        }

        if (!isset($req['contact_number'])) {
            $req['contact_number'] = null;
        }

        if (isset($req['title']) && !empty($req['title'])) {
            $req['slug'] = $this->createSlug($req['title']);
        }

        $db->update('posts', $id, array_keys($req), $req);
        flash('success', 'پست با موفقیت به‌روزرسانی شد');
        $this->redirect('admin/posts/index/1');
    }

    public function deleted($id)
    {
        $db = new \DataBase();
        $post = $db->new_select('*', 'posts', 'id', $id);
        
        if ($post) {
            if (!empty($post['img'])) {
                $this->removeImage($post['img']);
            }
            $db->delete('posts', $id);
            flash('success', 'پست با موفقیت حذف شد');
        } else {
            flash('error', 'پست یافت نشد');
        }
        
        $this->redirectBack();
    }

    public function status($id)
    {
        $db = new \DataBase();
        $post = $db->new_select('*', 'posts', 'id', $id);
        
        if ($post) {
            if ($post['status'] == 1) {
                $db->update('posts', $id, ['status'], [0]);
                flash('success', 'پست غیرفعال شد');
            } else {
                $db->update('posts', $id, ['status'], [1]);
                flash('success', 'پست فعال شد');
            }
        } else {
            flash('error', 'پست یافت نشد');
        }
        
        $this->redirectBack();
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