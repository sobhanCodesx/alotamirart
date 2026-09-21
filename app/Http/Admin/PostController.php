<?php
namespace App\Http\Admin;

class PostController extends AdminController
{
    public function index($page)
    {
        list($page, $offset) = $this->page($page, 20);
        $post = $this->db->fetchAll(
            'SELECT p.*, (SELECT name FROM users u WHERE u.id = p.user_id) AS w
             FROM posts p ORDER BY p.created_at DESC LIMIT ' . $offset . ', 20'
        );
        $total = (int) $this->db->value('SELECT COUNT(*) FROM posts');

        return $this->render('them/admin/pages/posts/index.php', [
            'menus' => $this->db->fetchAll('SELECT * FROM menu'),
            'post' => $post,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / 20)),
        ]);
    }

    public function create()
    {
        return $this->render('them/admin/pages/posts/create.php', [
            'menus' => $this->db->fetchAll('SELECT * FROM menu'),
        ]);
    }

    public function store()
    {
        $data = $this->request->input();
        $file = $this->request->file('img');

        if ($file) {
            $saved = $this->uploads->image($file, 'them/admin/dist/img', 'post-');
            if ($saved) $data['img'] = $saved;
        }

        if (!empty($data['title'])) $data['slug'] = $this->slug($data['title']);
        if (!array_key_exists('contact_number', $data)) $data['contact_number'] = null;

        $this->db->insert('posts', $data);
        flash('success', 'پست با موفقیت ایجاد شد');
        $this->redirect('admin/posts/index/1');
    }

    public function edit($id)
    {
        $post = $this->db->find('posts', (int) $id);
        if (!$post) {
            flash('error', 'پست یافت نشد');
            $this->redirect('admin/posts/index/1');
        }

        return $this->render('them/admin/pages/posts/update.php', [
            'menus' => $this->db->fetchAll('SELECT * FROM menu'),
            'post' => $post,
        ]);
    }

    public function update($id)
    {
        $id = (int) $id;
        $current = $this->db->find('posts', $id);
        if (!$current) {
            flash('error', 'پست یافت نشد');
            $this->redirect('admin/posts/index/1');
        }

        $data = $this->request->input();
        $file = $this->request->file('img');

        if ($file) {
            $saved = $this->uploads->image($file, 'them/admin/dist/img', 'post-');
            if ($saved) {
                if (!empty($current['img'])) $this->uploads->remove($current['img']);
                $data['img'] = $saved;
            }
        }

        if (!empty($data['title'])) $data['slug'] = $this->slug($data['title']);
        if (!array_key_exists('contact_number', $data)) $data['contact_number'] = null;

        $this->db->updateById('posts', $id, $data);
        flash('success', 'پست با موفقیت به‌روزرسانی شد');
        $this->redirect('admin/posts/index/1');
    }

    public function delete($id)
    {
        $post = $this->db->find('posts', (int) $id);
        if ($post) {
            if (!empty($post['img'])) $this->uploads->remove($post['img']);
            $this->db->deleteById('posts', (int) $id);
            flash('success', 'پست با موفقیت حذف شد');
        }
        $this->back('admin/posts/index/1');
    }

    public function status($id)
    {
        $post = $this->db->find('posts', (int) $id);
        if ($post) {
            $this->db->updateById('posts', (int) $id, ['status' => (int) !$post['status']]);
        }
        $this->back('admin/posts/index/1');
    }
}
