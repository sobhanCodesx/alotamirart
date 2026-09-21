<?php
namespace App\Http\Panel;

class BrandController extends PanelController
{
    public function index($page)
    {
        list($page, $offset) = $this->page($page, 5);
        $userId = (int) $this->user['id'];
        $post = $this->db->fetchAll(
            'SELECT * FROM post_brand WHERE user_id = ? ORDER BY id DESC LIMIT ' . $offset . ', 5',
            [$userId]
        );
        $total = (int) $this->db->value('SELECT COUNT(*) FROM post_brand WHERE user_id = ?', [$userId]);

        return $this->render('them/panel/brand/index.php', [
            'menus' => $this->db->fetchAll('SELECT * FROM menu'),
            'post' => $post,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / 5)),
        ], true);
    }

    public function create()
    {
        return $this->render('them/panel/brand/create.php', [
            'item' => $this->db->fetchAll('SELECT * FROM items_brands'),
        ], true);
    }

    public function store()
    {
        $data = $this->request->input();
        $data['user_id'] = (int) $this->user['id'];

        if (!$this->validSlug(isset($data['slug']) ? $data['slug'] : '')) {
            flash('msg-post', 'لطفا نوار آدرس را انگلیسی پر کنید');
            $this->back('userbrand/create');
        }

        $file = $this->request->file('img');
        if (!$file || !($saved = $this->uploads->image($file, 'them/admin/dist/img', 'brand-post-'))) {
            flash('msg-post', 'لطفا عکس را وارد کنید');
            $this->back('userbrand/create');
        }
        $data['img'] = $saved;

        $this->db->insert('post_brand', $data);
        $this->redirect('user/brand/1');
    }

    public function edit($id)
    {
        $post = $this->owned((int) $id);
        if (!$post) $this->redirect('user/brand/1');

        return $this->render('them/panel/brand/update.php', [
            'item' => $this->db->fetchAll('SELECT * FROM items_brands'),
            'post' => $post,
        ], true);
    }

    public function update($id)
    {
        $id = (int) $id;
        $current = $this->owned($id);
        if (!$current) $this->redirect('user/brand/1');

        $data = $this->request->input();
        $data['user_id'] = (int) $this->user['id'];

        if (!$this->validSlug(isset($data['slug']) ? $data['slug'] : '')) {
            flash('msg-post', 'لطفا نوار آدرس را انگلیسی پر کنید');
            $this->back('user/brand/1');
        }

        $file = $this->request->file('img');
        if ($file && ($saved = $this->uploads->image($file, 'them/admin/dist/img', 'brand-post-'))) {
            if (!empty($current['img'])) $this->uploads->remove($current['img']);
            $data['img'] = $saved;
        }

        $this->db->updateById('post_brand', $id, $data);
        $this->redirect('user/brand/1');
    }

    private function owned($id)
    {
        return $this->db->fetch('SELECT * FROM post_brand WHERE id = ? AND user_id = ? LIMIT 1', [$id, (int) $this->user['id']]);
    }

    private function validSlug($slug)
    {
        return $slug !== '' && preg_match('/^[A-Za-z0-9\-_.]+$/', $slug);
    }
}
