<?php
namespace App\Http\Admin;

class MenuController extends AdminController
{
    public function index($page)
    {
        list($page, $offset) = $this->page($page, 20);
        $menu = $this->db->fetchAll('SELECT * FROM menu ORDER BY id DESC LIMIT ' . $offset . ', 20');
        $total = (int) $this->db->value('SELECT COUNT(*) FROM menu');

        return $this->render('them/admin/pages/menu/index.php', [
            'menu' => $menu,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / 20)),
        ]);
    }

    public function create()
    {
        return $this->render('them/admin/pages/menu/create.php');
    }

    public function store()
    {
        $data = $this->request->input();
        if (!empty($data['title']) && empty($data['slug'])) $data['slug'] = $this->slug($data['title']);
        $this->db->insert('menu', $data);
        $this->site->clear();
        $this->redirect('admin/menu/index/1');
    }

    public function edit($id)
    {
        return $this->render('them/admin/pages/menu/update.php', [
            'men' => $this->db->find('menu', (int) $id),
        ]);
    }

    public function update($id)
    {
        $data = $this->request->input();
        if (!empty($data['title']) && empty($data['slug'])) $data['slug'] = $this->slug($data['title']);
        $this->db->updateById('menu', (int) $id, $data);
        $this->site->clear();
        $this->redirect('admin/menu/index/1');
    }

    public function delete($id)
    {
        $this->db->deleteById('menu', (int) $id);
        $this->site->clear();
        $this->back('admin/menu/index/1');
    }
}
