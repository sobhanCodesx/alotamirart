<?php
namespace App\Http\Panel;

class PostController extends PanelController
{
    public function index($page)
    {
        list($page, $offset) = $this->page($page, 10);
        $userId = (int) $this->user['id'];
        $post = $this->db->fetchAll(
            'SELECT * FROM posts WHERE user_id = ? ORDER BY id DESC LIMIT ' . $offset . ', 10',
            [$userId]
        );
        $total = (int) $this->db->value('SELECT COUNT(*) FROM posts WHERE user_id = ?', [$userId]);

        return $this->render('them/panel/posts/index.php', [
            'menus' => $this->db->fetchAll('SELECT * FROM menu'),
            'post' => $post,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / 10)),
        ], true);
    }

    public function create()
    {
        return $this->render('them/panel/posts/create.php', [
            'menus' => $this->db->fetchAll('SELECT * FROM menu'),
        ], true);
    }

    public function store()
    {
        $data = $this->request->input();
        $data['user_id'] = (int) $this->user['id'];
        if (!empty($data['title'])) $data['slug'] = $this->slug($data['title']);

        $file = $this->request->file('img');
        if ($file && ($saved = $this->uploads->image($file, 'them/admin/dist/img', 'post-'))) $data['img'] = $saved;

        $this->db->insert('posts', $data);
        flash('success', 'پست با موفقیت ایجاد شد');
        $this->redirect('user/post/1');
    }

    public function edit($id)
    {
        $post = $this->owned((int) $id);
        if (!$post) {
            flash('error', 'پست یافت نشد');
            $this->redirect('user/post/1');
        }

        return $this->render('them/panel/posts/update.php', [
            'menus' => $this->db->fetchAll('SELECT * FROM menu'),
            'post' => $post,
        ], true);
    }

    public function update($id)
    {
        $id = (int) $id;
        $current = $this->owned($id);
        if (!$current) $this->redirect('user/post/1');

        $data = $this->request->input();
        if (empty($data['title']) || empty($data['content'])) {
            flash('error', 'عنوان و محتوا الزامی هستند');
            $this->back('user/post/1');
        }

        $data['slug'] = $this->slug($data['title']);
        $data['user_id'] = (int) $this->user['id'];

        $file = $this->request->file('img');
        if ($file && ($saved = $this->uploads->image($file, 'them/admin/dist/img', 'post-'))) {
            if (!empty($current['img'])) $this->uploads->remove($current['img']);
            $data['img'] = $saved;
        }

        $this->db->updateById('posts', $id, $data);
        flash('success', 'پست با موفقیت به‌روزرسانی شد');
        $this->redirect('user/post/1');
    }

    private function owned($id)
    {
        return $this->db->fetch('SELECT * FROM posts WHERE id = ? AND user_id = ? LIMIT 1', [$id, (int) $this->user['id']]);
    }
}
