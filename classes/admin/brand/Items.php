<?php

class Items extends Admin
{
    public function index($page)
    {
        $db = new \DataBase();
        $prepage = 10;
        $page = max(1, (int) $page);
        $start = ($page - 1) * $prepage;

        $item = $db->select("SELECT * FROM items_brands ORDER BY id DESC LIMIT {$start},{$prepage}")->fetchAll();
        $count = $db->select("SELECT COUNT(`id`) AS total FROM items_brands")->fetch();
        $total = isset($count['total']) ? (int) $count['total'] : 0;
        $pages = max(1, (int) ceil($total / $prepage));

        require_once BASE_PATH . '/them/admin/pages/brand_items/index.php';
    }

    public function created($req)
    {
        $db = new \DataBase();

        if (empty($req['name'])) {
            flash('error', 'عنوان برند را وارد کنید');
            $this->redirectBack('admin/brands/index/1');
        }

        if (empty($req['img']) || !is_array($req['img']) || empty($req['img']['tmp_name'])) {
            flash('error', 'تصویر برند را انتخاب کنید');
            $this->redirectBack('admin/brands/index/1');
        }

        $savedImage = $this->saveImage($req['img'], 'brand/');
        if (!$savedImage) {
            flash('error', 'آپلود تصویر برند انجام نشد');
            $this->redirectBack('admin/brands/index/1');
        }

        $req['img'] = $savedImage;
        $id = $db->insert('items_brands', array_keys($req), array_values($req));
        if ($id === false) {
            $this->removeImage($savedImage);
            flash('error', 'ثبت برند انجام نشد');
            $this->redirectBack('admin/brands/index/1');
        }

        flash('success', 'برند با موفقیت ایجاد شد');
        $this->redirect('admin/brands/index/1');
    }

    public function update($id)
    {
        $db = new \DataBase();
        $item = $db->new_select('*', 'items_brands', 'id', $id);
        if (!$item) {
            flash('error', 'برند یافت نشد');
            $this->redirect('admin/brands/index/1');
        }
        require_once BASE_PATH . '/them/admin/pages/brand_items/update.php';
    }

    public function updated($req, $id)
    {
        $db = new \DataBase();
        $current = $db->new_select('*', 'items_brands', 'id', $id);
        if (!$current) {
            flash('error', 'برند یافت نشد');
            $this->redirect('admin/brands/index/1');
        }

        if (empty(trim(isset($req['name']) ? $req['name'] : ''))) {
            flash('error', 'عنوان برند را وارد کنید');
            $this->redirectBack('admin/brands/update/' . (int) $id);
        }

        $newImage = null;
        if (isset($req['img']) && is_array($req['img']) && !empty($req['img']['tmp_name'])) {
            $newImage = $this->saveImage($req['img'], 'brand/');
            if (!$newImage) {
                flash('error', 'آپلود تصویر جدید انجام نشد و تصویر قبلی حفظ شد');
                $this->redirectBack('admin/brands/update/' . (int) $id);
            }
            $req['img'] = $newImage;
        } else {
            unset($req['img']);
        }

        if (!$db->update('items_brands', $id, array_keys($req), array_values($req))) {
            if ($newImage) $this->removeImage($newImage);
            flash('error', 'بروزرسانی برند انجام نشد');
            $this->redirectBack('admin/brands/update/' . (int) $id);
        }

        if ($newImage && !empty($current['img'])) {
            $this->removeImage($current['img']);
        }

        flash('success', 'برند با موفقیت بروزرسانی شد');
        $this->redirect('admin/brands/index/1');
    }

    public function deleted($id)
    {
        $db = new \DataBase();
        $item = $db->new_select('*', 'items_brands', 'id', $id);

        if (!$item) {
            flash('error', 'برند یافت نشد');
            $this->redirectBack('admin/brands/index/1');
        }

        if ($db->delete('items_brands', $id)) {
            if (!empty($item['img'])) $this->removeImage($item['img']);
            flash('success', 'برند حذف شد');
        } else {
            flash('error', 'حذف برند انجام نشد');
        }

        $this->redirectBack('admin/brands/index/1');
    }
}
