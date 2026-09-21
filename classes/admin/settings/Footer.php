<?php

class Footer extends Admin
{
    public function index()
    {
        $db = new DataBase();
        $data = $db->getLastInsert('footer');
        require_once BASE_PATH . "/them/admin/pages/setting/footer.php";
    }

    public function create($req)
    {
        $db = new \DataBase();
        if (empty($req)) $this->redirectBack('admin/settings/footer');

        $id = !empty($req['id']) ? (int) $req['id'] : 0;
        $current = $id ? $db->new_select('*', 'footer', 'id', $id) : null;
        $newImage = null;
        $oldImage = null;

        if (isset($req['img_footer']) && is_array($req['img_footer']) && !empty($req['img_footer']['tmp_name'])) {
            $newImage = $this->saveImage($req['img_footer'], uniqid('footer-', true));
            if (!$newImage) {
                flash('msg-post', 'آپلود تصویر فوتر انجام نشد');
                $this->redirectBack('admin/settings/footer');
            }
            $req['img_footer'] = $newImage;
            if ($current && !empty($current['img_footer'])) $oldImage = $current['img_footer'];
        } else {
            unset($req['img_footer']);
        }

        if (!$id && !$newImage) {
            flash('msg-post', 'لطفا عکس فوتر را وارد کنید');
            $this->redirectBack('admin/settings/footer');
        }

        unset($req['id']);
        $ok = $id
            ? $db->update('footer', $id, array_keys($req), array_values($req))
            : $db->insert('footer', array_keys($req), array_values($req)) !== false;

        if (!$ok) {
            if ($newImage) $this->removeImage($newImage);
            flash('msg-post', 'ذخیره تنظیمات فوتر انجام نشد');
            $this->redirectBack('admin/settings/footer');
        }

        if ($oldImage) $this->removeImage($oldImage);
        flash('SuccessFooter', $id ? 'اطلاعات بروز شد' : 'اطلاعات ثبت شد');
        $this->redirectBack('admin/settings/footer');
    }
}
