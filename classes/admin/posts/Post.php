<?php

class Post extends Admin
{
    public function index($page)
    {

        $db = new \DataBase();
      
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        /////
        $prepage = 20;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        $post = $db->select("SELECT *,(SELECT name FROM users WHERE users.id = posts.user_id) AS w FROM posts ORDER BY created_at DESC LIMIT {$start},{$prepage} ")->fetchAll();
        $count = $db->select("SELECT COUNT(`id`) FROM posts")->fetch();
        $pages = ceil($count[0] / $prepage);
        require_once BASE_PATH . '/them/admin/pages/posts/index.php';
    }

    public function create()
    {
        $db = new \DataBase();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        require_once BASE_PATH . '/them/admin/pages/posts/create.php';
    }

    public function created($req)
    {
        $db = new \DataBase();
        if ($req['img']['tmp_name'] != null) {
            $req['img'] = $this->saveImage($req['img'],'img-');
        }else{
            unset($req['img']);
        }
        $db->insert('posts', array_keys($req), $req);
        $this->redirect('admin/posts/index/1');
    }

    public function update($id)
    {
        $db = new \DataBase();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $post = $db->new_select('*', 'posts', 'id', $id);
        require_once BASE_PATH . '/them/admin/pages/posts/update.php';
    }

    public function updated($req, $id)
    {
        $db = new \DataBase();
        if ($req['img']['tmp_name'] != null) {
            $post = $db->new_select('*', 'posts', 'id', $id);
            $this->removeImage($post['img']);
            $req['img'] = $this->saveImage($req['img'], 'img');
        } else {
            unset($req['img']);
        }
        $db->update('posts', $id, array_keys($req), $req);
        $this->redirect('admin/posts/index/1');
    }

    public function deleted($id)
    {
        $db = new \DataBase();
        $post = $db->new_select('*', 'posts', 'id', $id);
        if ($post)
        {
            $db->delete('posts', $id);
        }
        $this->redirectBack();
    }
    public function status($id)
    {
        $db = new \DataBase();
        $post = $db->new_select('*', 'posts', 'id', $id);
        if($post['status'] == 1)
        {
            $db->update('posts',$id,['status'],[0]);
            $this->redirectBack();
        }else{
            $db->update('posts',$id,['status'],[1]);
            $this->redirectBack();
        }
        $this->redirectBack(); 
    }
}
