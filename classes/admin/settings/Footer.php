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
        $randone = rand(1,400);
        if (empty($req)) $this->redirectBack();

        if (empty($req['id'])) {

            if ($req['img_footer']['tmp_name'] != null) {

                $req['img_footer'] = $this->saveImage($req['img_footer'], $randone.'-img');

            } else {
                flash('msg-post', 'لطفا  عکس را وارد کنید');
                $this->redirectBack();
                exit();
            }
            $db->insert('footer', array_keys($req), $req);
            flash('SuccessFooter', 'اطلاعات ثبت شد');
            $this->redirectBack();

        } else {
            $data = $db->select("SELECT * FROM footer WHERE id = ?",$req['id'])->fetch();
            if ($req['img_footer']['tmp_name'] != null) {
                $this->removeImage($data['img_footer']);
                $req['img_footer'] = $this->saveImage($req['img_footer'], $randone.'-img');
            } else {

                unset($req['img_footer']);
            }

            $db->update('footer', $req['id'], array_keys($req), $req);
            flash('SuccessFooter', 'اطلاعات بروز شد');
            $this->redirectBack();
        }
    }
}