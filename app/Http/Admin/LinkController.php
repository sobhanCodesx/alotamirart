<?php
namespace App\Http\Admin;

class LinkController extends AdminController
{
    public function index($page)
    {
        list($page, $offset) = $this->page($page, 10);
        $link = $this->db->fetchAll('SELECT * FROM link ORDER BY id DESC LIMIT ' . $offset . ', 10');
        $total = (int) $this->db->value('SELECT COUNT(*) FROM link');

        return $this->render('them/admin/pages/link.php', [
            'ostan' => $this->db->fetchAll('SELECT * FROM provinces'),
            'link' => $link,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / 10)),
        ]);
    }

    public function store()
    {
        $data = $this->request->input();
        if (!empty($data['title']) && !empty($data['link'])) $this->db->insert('link', $data);
        $this->redirect('backlink/admin/1');
    }

    public function delete($id)
    {
        $this->db->deleteById('link', (int) $id);
        $this->back('backlink/admin/1');
    }
}
