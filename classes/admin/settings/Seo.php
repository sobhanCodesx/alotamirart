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
        $randone = rand(1,100);
        $randtwo = rand(100,200);
        if (empty($req)) $this->redirectBack();

        if (empty($req['id'])) {

            if ($req['logo']['tmp_name'] != null and $req['header']['tmp_name'] != null) {

                $req['logo'] = $this->saveImage($req['logo'], $randone.'-img');
                $req['header'] = $this->saveImage($req['header'], $randtwo.'-img');

            } else {
                flash('msg-post', 'لطفا  عکس را وارد کنید');
                $this->redirectBack();
                exit();
            }
            $db->insert('seo', array_keys($req), $req);
            flash('SuccessSeo', 'اطلاعات ثبت شد');
            $this->redirectBack();

        } else {
            $data = $db->select("SELECT * FROM seo WHERE id = ?",$req['id'])->fetch();
            if ($req['logo']['tmp_name'] != null) {
                $this->removeImage($data['logo']);
                $req['logo'] = $this->saveImage($req['logo'], $randone.'-img');
            } else {

                unset($req['logo']);
            }

            if ($req['header']['tmp_name'] != null) {
                $this->removeImage($data['header']);
                $req['header'] = $this->saveImage($req['header'], $randtwo.'-img');
            } else {
                unset($req['header']);
            }


            $db->update('seo', $req['id'], array_keys($req), $req);
            flash('SuccessSeo', 'اطلاعات بروز شد');
            $this->redirectBack();
        }
    }
}