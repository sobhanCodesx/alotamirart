<?php

class Seo extends Admin
{
    public function index()
    {
        $db = new DataBase();
        $data = $db->getLastInsert('seo');
        require_once BASE_PATH . "/them/admin/pages/setting/seo.php";
    }

    public function create($req)
    {
        $db = new \DataBase();
        if (empty($req)) $this->redirectBack('admin/settings/seo');

        $id = !empty($req['id']) ? (int) $req['id'] : 0;
        $current = $id ? $db->new_select('*', 'seo', 'id', $id) : null;
        $oldImages = [];
        $newImages = [];

        foreach (['logo', 'header'] as $field) {
            if (isset($req[$field]) && is_array($req[$field]) && !empty($req[$field]['tmp_name'])) {
                $saved = $this->saveImage($req[$field], uniqid($field . '-', true));
                if (!$saved) {
                    foreach ($newImages as $path) $this->removeImage($path);
                    flash('msg-post', 'آپلود تصویر انجام نشد');
                    $this->redirectBack('admin/settings/seo');
                }
                $req[$field] = $saved;
                $newImages[$field] = $saved;
                if ($current && !empty($current[$field])) $oldImages[$field] = $current[$field];
            } else {
                unset($req[$field]);
            }
        }

        if (!$id && (!isset($newImages['logo']) || !isset($newImages['header']))) {
            foreach ($newImages as $path) $this->removeImage($path);
            flash('msg-post', 'لطفا عکس‌ها را وارد کنید');
            $this->redirectBack('admin/settings/seo');
        }

        if ($id) {
            unset($req['id']);
            $ok = $db->update('seo', $id, array_keys($req), array_values($req));
        } else {
            unset($req['id']);
            $ok = $db->insert('seo', array_keys($req), array_values($req)) !== false;
        }

        if (!$ok) {
            foreach ($newImages as $path) $this->removeImage($path);
            flash('msg-post', 'ذخیره تنظیمات سئو انجام نشد');
            $this->redirectBack('admin/settings/seo');
        }

        foreach ($oldImages as $path) $this->removeImage($path);
        flash('SuccessSeo', $id ? 'اطلاعات بروز شد' : 'اطلاعات ثبت شد');
        $this->redirectBack('admin/settings/seo');
    }
}
