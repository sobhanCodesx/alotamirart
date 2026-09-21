<?php
namespace App\Http\Admin;

class BrandPostController extends AdminController
{
    public function index($page)
    {
        list($page, $offset) = $this->page($page, 20);
        $post = $this->db->fetchAll(
            'SELECT pb.*, (SELECT name FROM users u WHERE u.id = pb.user_id) AS w
             FROM post_brand pb ORDER BY pb.created_at DESC LIMIT ' . $offset . ', 20'
        );
        $total = (int) $this->db->value('SELECT COUNT(*) FROM post_brand');

        return $this->render('them/admin/pages/brand_posts/index.php', [
            'menus' => $this->db->fetchAll('SELECT * FROM menu'),
            'post' => $post,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / 20)),
        ]);
    }

    public function create()
    {
        return $this->render('them/admin/pages/brand_posts/create.php', [
            'menus' => $this->db->fetchAll('SELECT * FROM menu'),
            'item' => $this->db->fetchAll('SELECT * FROM items_brands'),
        ]);
    }

    public function store()
    {
        $data = $this->request->input();
        if (!$this->validSlug(isset($data['slug']) ? $data['slug'] : '')) {
            flash('msg-post', 'لطفا نوار آدرس را انگلیسی پر کنید');
            $this->back('admin/brands/blog/create');
        }

        $file = $this->request->file('img');
        if (!$file || !($saved = $this->uploads->image($file, 'them/admin/dist/img', 'brand-post-'))) {
            flash('msg-post', 'لطفا عکس را وارد کنید');
            $this->back('admin/brands/blog/create');
        }
        $data['img'] = $saved;

        $this->db->insert('post_brand', $data);
        $this->redirect('admin/brands/post/1');
    }

    public function edit($id)
    {
        return $this->render('them/admin/pages/brand_posts/update.php', [
            'item' => $this->db->fetchAll('SELECT * FROM items_brands'),
            'post' => $this->db->find('post_brand', (int) $id),
        ]);
    }

    public function update($id)
    {
        $id = (int) $id;
        $current = $this->db->find('post_brand', $id);
        $data = $this->request->input();

        if (empty($data['title'])) {
            flash('msg-post', 'لطفا عنوان را پر کنید');
            $this->back('admin/brands/post/1');
        }
        if (!$this->validSlug(isset($data['slug']) ? $data['slug'] : '')) {
            flash('msg-post', 'لطفا نوار آدرس را انگلیسی پر کنید');
            $this->back('admin/brands/post/1');
        }

        $file = $this->request->file('img');
        if ($file && ($saved = $this->uploads->image($file, 'them/admin/dist/img', 'brand-post-'))) {
            if ($current && !empty($current['img'])) $this->uploads->remove($current['img']);
            $data['img'] = $saved;
        }

        $this->db->updateById('post_brand', $id, $data);
        $this->redirect('admin/brands/post/1');
    }

    public function delete($id)
    {
        $post = $this->db->find('post_brand', (int) $id);
        if ($post) {
            if (!empty($post['img'])) $this->uploads->remove($post['img']);
            $this->db->deleteById('post_brand', (int) $id);
        }
        $this->back('admin/brands/post/1');
    }

    public function status($id)
    {
        $post = $this->db->find('post_brand', (int) $id);
        if ($post) $this->db->updateById('post_brand', (int) $id, ['status' => (int) !$post['status']]);
        $this->back('admin/brands/post/1');
    }

    private function validSlug($slug)
    {
        return $slug !== '' && preg_match('/^[A-Za-z0-9\-_.]+$/', $slug);
    }
}
