<?php

class Menu extends Admin
{
    public function index($page)
    {

        $db = new \DataBase();
      
        /////
        $prepage = 20;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        $menu = $db->select("SELECT * FROM menu ORDER BY id DESC LIMIT {$start},{$prepage} ")->fetchAll();
        $count = $db->select("SELECT COUNT(`id`) FROM menu")->fetch();
        $pages = ceil($count[0] / $prepage);
        require_once BASE_PATH . '/them/admin/pages/menu/index.php';
    }
    public function create()
    {
        require_once BASE_PATH . '/them/admin/pages/menu/create.php';
    }
    public function created($req)
    {
        $db = new \DataBase();
        $db->insert('menu', array_keys($req), $req);
        $this->redirect('admin/menu/index/1');

    }

    public function update($id)
    {
        $db = new \DataBase();
        $men =  $db->new_select('*', 'menu', 'id', $id);
        require_once BASE_PATH . '/them/admin/pages/menu/update.php';
    }
    public function updated($req, $id)
    {
        $db = new \DataBase();
        $db->update('menu', $id, array_keys($req), $req);
        $this->redirect('admin/menu/index/1');
    }
    public function deleted($id)
    {
        $db = new \DataBase();
        $db->delete('menu', $id);
        $this->redirectBack();
    }
}
