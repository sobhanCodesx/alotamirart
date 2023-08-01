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
        $randone = rand(1,100);
        $randtwo = rand(100,200);
        $randtree = rand(200,300);
        if (empty($req)) $this->redirectBack();

        if (empty($req['id'])) {

            if ($req['img_one']['tmp_name'] != null and $req['img_two']['tmp_name'] != null and $req['img_tree']['tmp_name'] != null) {

                $req['img_one'] = $this->saveImage($req['img_one'], $randone.'-img-img_one');
                $req['img_two'] = $this->saveImage($req['img_two'], $randtwo.'-img-img_two');
                $req['img_tree'] = $this->saveImage($req['img_tree'], $randtree.'-img-img_tree');

            } else {
                flash('msg-post', 'لطفا  عکس را وارد کنید');
                $this->redirectBack();
                exit();
            }
            $db->insert('header', array_keys($req), $req);
            flash('SuccessHeader', 'اطلاعات ثبت شد');
            $this->redirectBack();

        } else {

            $data = $db->select("SELECT * FROM header WHERE id = ?",$req['id'])->fetch();



            if ($req['img_one']['tmp_name'] != null) {
                $this->removeImage($data['img_one']);
                $req['img_one'] = $this->saveImage($req['img_one'], $randone.'-img_img-one');
            } else {

                unset($req['img_one']);
            }

            if ($req['img_two']['tmp_name'] != null) {
                $this->removeImage($data['img_two']);
                $req['img_two'] = $this->saveImage($req['img_two'], $randtwo.'-img_img-tree');
            } else {
                unset($req['img_two']);
            }

            if ($req['img_tree']['tmp_name'] != null) {
                $this->removeImage($data['img_tree']);
                $req['img_tree'] = $this->saveImage($req['img_tree'], $randtree.'-img_img-tree');
            } else {
                unset($req['img_tree']);
            }
            $db->update('header', $req['id'], array_keys($req), $req);
            flash('SuccessHeader', 'اطلاعات بروز شد');
            $this->redirectBack();
        }
    }
}
