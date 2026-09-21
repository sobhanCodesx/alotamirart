<?php

class BrandPosts extends Admin
{
    public function index($page)
    {
        $db = new \DataBase();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();

        $prepage = 20;
        $page = max(1, (int) $page);
        $start = ($page - 1) * $prepage;

        $post = $db->select(
            "SELECT *,(SELECT name FROM users WHERE users.id = post_brand.user_id) AS w
             FROM post_brand ORDER BY created_at DESC LIMIT {$start},{$prepage}"
        )->fetchAll();

        $count = $db->select("SELECT COUNT(`id`) AS total FROM post_brand")->fetch();
        $total = isset($count['total']) ? (int) $count['total'] : 0;
        $pages = max(1, (int) ceil($total / $prepage));

        require_once BASE_PATH . '/them/admin/pages/brand_posts/index.php';
    }

    public function create()
    {
        $db = new \DataBase();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $item = $db->select("SELECT * FROM items_brands")->fetchAll();
        require_once BASE_PATH . '/them/admin/pages/brand_posts/create.php';
    }

    public function created($req)
    {
        $db = new \DataBase();

        if (empty($req['slug']) || !$this->validSlug($req['slug'])) {
            flash('msg-post', 'لطفا نوار آدرس را انگلیسی پر کنید');
            $this->redirectBack('admin/brands/blog/create');
        }

        if (empty($req['img']) || !is_array($req['img']) || empty($req['img']['tmp_name'])) {
            flash('msg-post', 'لطفا عکس را وارد کنید');
            $this->redirectBack('admin/brands/blog/create');
        }

        $saved = $this->saveImage($req['img'], 'img');
        if (!$saved) {
            flash('msg-post', 'آپلود عکس انجام نشد');
            $this->redirectBack('admin/brands/blog/create');
        }

        $req['img'] = $saved;
        $id = $db->insert('post_brand', array_keys($req), array_values($req));
        if ($id === false) {
            $this->removeImage($saved);
            flash('msg-post', 'ثبت مقاله برند انجام نشد');
            $this->redirectBack('admin/brands/blog/create');
        }

        flash('success', 'مقاله برند ایجاد شد');
        $this->redirect('admin/brands/post/1');
    }

    public function update($id)
    {
        $db = new \DataBase();
        $item = $db->select("SELECT * FROM items_brands")->fetchAll();
        $post = $db->new_select('*', 'post_brand', 'id', $id);

        if (!$post) {
            flash('error', 'مقاله برند یافت نشد');
            $this->redirect('admin/brands/post/1');
        }

        require_once BASE_PATH . '/them/admin/pages/brand_posts/update.php';
    }

    public function updated($req, $id)
    {
        $db = new \DataBase();
        $current = $db->new_select('*', 'post_brand', 'id', $id);

        if (!$current) {
            flash('error', 'مقاله برند یافت نشد');
            $this->redirect('admin/brands/post/1');
        }

        if (empty($req['title'])) {
            flash('msg-post', 'لطفا عنوان را پر کنید');
            $this->redirectBack('admin/brands/post/update/' . (int) $id);
        }

        if (empty($req['slug']) || !$this->validSlug($req['slug'])) {
            flash('msg-post', 'لطفا نوار آدرس را انگلیسی پر کنید');
            $this->redirectBack('admin/brands/post/update/' . (int) $id);
        }

        $newImage = null;
        if (isset($req['img']) && is_array($req['img']) && !empty($req['img']['tmp_name'])) {
            $newImage = $this->saveImage($req['img'], 'img');
            if (!$newImage) {
                flash('msg-post', 'آپلود تصویر جدید انجام نشد و تصویر قبلی حفظ شد');
                $this->redirectBack('admin/brands/post/update/' . (int) $id);
            }
            $req['img'] = $newImage;
        } else {
            unset($req['img']);
        }

        if (!$db->update('post_brand', $id, array_keys($req), array_values($req))) {
            if ($newImage) $this->removeImage($newImage);
            flash('msg-post', 'بروزرسانی مقاله برند انجام نشد');
            $this->redirectBack('admin/brands/post/update/' . (int) $id);
        }

        if ($newImage && !empty($current['img'])) {
            $this->removeImage($current['img']);
        }

        flash('success', 'مقاله برند بروزرسانی شد');
        $this->redirect('admin/brands/post/1');
    }

    public function deleted($id)
    {
        $db = new \DataBase();
        $post = $db->new_select('*', 'post_brand', 'id', $id);

        if (!$post) {
            flash('error', 'مقاله برند یافت نشد');
            $this->redirectBack('admin/brands/post/1');
        }

        if ($db->delete('post_brand', $id)) {
            if (!empty($post['img'])) $this->removeImage($post['img']);
            flash('success', 'مقاله برند حذف شد');
        } else {
            flash('error', 'حذف مقاله برند انجام نشد');
        }

        $this->redirectBack('admin/brands/post/1');
    }

    public function status($id)
    {
        $db = new \DataBase();
        $post = $db->new_select('*', 'post_brand', 'id', $id);

        if (!$post) {
            flash('error', 'مقاله برند یافت نشد');
            $this->redirectBack('admin/brands/post/1');
        }

        $status = !empty($post['status']) ? 0 : 1;
        if (!$db->update('post_brand', $id, ['status'], [$status])) {
            flash('error', 'تغییر وضعیت انجام نشد');
        }

        $this->redirectBack('admin/brands/post/1');
    }

    private function validSlug($slug)
    {
        return (bool) preg_match('/^[A-Za-z0-9._-]+$/', trim((string) $slug));
    }
}
