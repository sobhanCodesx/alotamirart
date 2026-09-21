<?php

class Link extends Admin
{
    public function index($page)
    {
        $db = new \DataBase();
        $ostan = $db->select("SELECT * FROM provinces")->fetchAll();

        $prepage = 10;
        $page = max(1, (int) $page);
        $start = ($page - 1) * $prepage;

        $link = $db->select("SELECT * FROM link ORDER BY id DESC LIMIT {$start},{$prepage}")->fetchAll();
        $count = $db->select("SELECT COUNT(`id`) AS total FROM link")->fetch();
        $total = isset($count['total']) ? (int) $count['total'] : 0;
        $pages = max(1, (int) ceil($total / $prepage));

        require_once BASE_PATH . '/them/admin/pages/link.php';
    }

    public function create($req)
    {
        $db = new \DataBase();
        $title = isset($req['title']) ? trim($req['title']) : '';
        $link = isset($req['link']) ? trim($req['link']) : '';

        if ($title === '' || $link === '') {
            flash('error', 'عنوان و لینک را وارد کنید');
            $this->redirectBack('backlink/admin/1');
        }

        if ($db->insert('link', array_keys($req), array_values($req)) === false) {
            flash('error', 'ثبت لینک انجام نشد');
            $this->redirectBack('backlink/admin/1');
        }

        flash('success', 'لینک ثبت شد');
        $this->redirect('backlink/admin/1');
    }

    public function delete($id)
    {
        $db = new DataBase();
        if ($db->delete('link', $id)) {
            flash('success', 'لینک حذف شد');
        } else {
            flash('error', 'حذف لینک انجام نشد');
        }
        $this->redirectBack('backlink/admin/1');
    }
}
