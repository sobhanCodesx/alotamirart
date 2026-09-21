<?php

class Menu extends Admin
{
    public function index($page)
    {
        $db = new \DataBase();
        $prepage = 20;
        $page = max(1, (int) $page);
        $start = ($page - 1) * $prepage;

        $menu = $db->select("SELECT * FROM menu ORDER BY id DESC LIMIT {$start},{$prepage}")->fetchAll();
        $count = $db->select("SELECT COUNT(`id`) AS total FROM menu")->fetch();
        $total = isset($count['total']) ? (int) $count['total'] : 0;
        $pages = max(1, (int) ceil($total / $prepage));

        require_once BASE_PATH . '/them/admin/pages/menu/index.php';
    }

    public function create()
    {
        require_once BASE_PATH . '/them/admin/pages/menu/create.php';
    }

    public function created($req)
    {
        $db = new \DataBase();
        if ($db->insert('menu', array_keys($req), array_values($req)) === false) {
            flash('error', 'ایجاد منو انجام نشد');
            $this->redirectBack('admin/menu/create');
        }
        flash('success', 'منو با موفقیت ایجاد شد');
        $this->redirect('admin/menu/index/1');
    }

    public function update($id)
    {
        $db = new \DataBase();
        $men = $db->new_select('*', 'menu', 'id', $id);
        if (!$men) {
            flash('error', 'منو یافت نشد');
            $this->redirect('admin/menu/index/1');
        }
        require_once BASE_PATH . '/them/admin/pages/menu/update.php';
    }

    public function updated($req, $id)
    {
        $db = new \DataBase();
        if (!$db->update('menu', $id, array_keys($req), array_values($req))) {
            flash('error', 'بروزرسانی منو انجام نشد');
            $this->redirectBack('admin/menu/update/' . (int) $id);
        }
        flash('success', 'منو با موفقیت بروزرسانی شد');
        $this->redirect('admin/menu/index/1');
    }

    public function deleted($id)
    {
        $db = new \DataBase();
        if ($db->delete('menu', $id)) {
            flash('success', 'منو حذف شد');
        } else {
            flash('error', 'حذف منو انجام نشد');
        }
        $this->redirectBack('admin/menu/index/1');
    }
}
