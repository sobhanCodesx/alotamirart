<?php
namespace App\Http\Admin;

class BrandController extends AdminController
{
    public function index($page)
    {
        list($page, $offset) = $this->page($page, 10);
        $item = $this->db->fetchAll('SELECT * FROM items_brands ORDER BY id DESC LIMIT ' . $offset . ', 10');
        $total = (int) $this->db->value('SELECT COUNT(*) FROM items_brands');

        return $this->render('them/admin/pages/brand_items/index.php', [
            'item' => $item,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / 10)),
        ]);
    }

    public function store()
    {
        $data = $this->request->input();
        if (empty($data['name'])) $this->redirect('admin/brands/index/1');

        $file = $this->request->file('img');
        if ($file) {
            $saved = $this->uploads->image($file, 'them/admin/dist/img/brand', 'brand-');
            if ($saved) $data['img'] = $saved;
        }

        if (empty($data['img'])) {
            flash('msg-post', 'لطفا عکس را وارد کنید');
            $this->redirect('admin/brands/index/1');
        }

        $this->db->insert('items_brands', $data);
        $this->redirect('admin/brands/index/1');
    }

    public function edit($id)
    {
        return $this->render('them/admin/pages/brand_items/update.php', [
            'item' => $this->db->find('items_brands', (int) $id),
        ]);
    }

    public function update($id)
    {
        $id = (int) $id;
        $current = $this->db->find('items_brands', $id);
        $data = $this->request->input();

        $file = $this->request->file('img');
        if ($file) {
            $saved = $this->uploads->image($file, 'them/admin/dist/img/brand', 'brand-');
            if ($saved) {
                if ($current && !empty($current['img'])) $this->uploads->remove($current['img']);
                $data['img'] = $saved;
            }
        }

        $this->db->updateById('items_brands', $id, $data);
        $this->redirect('admin/brands/index/1');
    }

    public function delete($id)
    {
        $item = $this->db->find('items_brands', (int) $id);
        if ($item) {
            if (!empty($item['img'])) $this->uploads->remove($item['img']);
            $this->db->deleteById('items_brands', (int) $id);
        }
        $this->back('admin/brands/index/1');
    }
}
