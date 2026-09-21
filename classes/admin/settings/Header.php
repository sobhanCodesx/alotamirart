<?php

class Header extends Admin
{
    public function index()
    {
        $db = new DataBase();
        $data = $db->getLastInsert('header');
        require_once BASE_PATH . "/them/admin/pages/setting/header.php";
    }

    public function create($req)
    {
        $db = new \DataBase();
        if (empty($req)) $this->redirectBack('admin/settings/header');

        $id = !empty($req['id']) ? (int) $req['id'] : 0;
        $current = $id ? $db->new_select('*', 'header', 'id', $id) : null;
        $fields = ['img_one', 'img_two', 'img_tree'];
        $oldImages = [];
        $newImages = [];

        foreach ($fields as $field) {
            if (isset($req[$field]) && is_array($req[$field]) && !empty($req[$field]['tmp_name'])) {
                $saved = $this->saveImage($req[$field], uniqid($field . '-', true));
                if (!$saved) {
                    foreach ($newImages as $path) $this->removeImage($path);
                    flash('msg-post', 'آپلود تصویر انجام نشد');
                    $this->redirectBack('admin/settings/header');
                }
                $req[$field] = $saved;
                $newImages[$field] = $saved;
                if ($current && !empty($current[$field])) $oldImages[$field] = $current[$field];
            } else {
                unset($req[$field]);
            }
        }

        if (!$id && count($newImages) !== count($fields)) {
            foreach ($newImages as $path) $this->removeImage($path);
            flash('msg-post', 'لطفا هر سه عکس را وارد کنید');
            $this->redirectBack('admin/settings/header');
        }

        unset($req['id']);
        $ok = $id
            ? $db->update('header', $id, array_keys($req), array_values($req))
            : $db->insert('header', array_keys($req), array_values($req)) !== false;

        if (!$ok) {
            foreach ($newImages as $path) $this->removeImage($path);
            flash('msg-post', 'ذخیره تنظیمات هدر انجام نشد');
            $this->redirectBack('admin/settings/header');
        }

        foreach ($oldImages as $path) $this->removeImage($path);
        flash('SuccessHeader', $id ? 'اطلاعات بروز شد' : 'اطلاعات ثبت شد');
        $this->redirectBack('admin/settings/header');
    }
}
