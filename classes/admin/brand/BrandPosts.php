<?php

class BrandPosts extends Admin
{
    public function index($page)
    {
        $db = new \DataBase();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        /////
        $prepage = 20;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        $post = $db->select("SELECT *,(SELECT name FROM users WHERE users.id = post_brand.user_id) AS w FROM post_brand ORDER BY created_at DESC LIMIT {$start},{$prepage} ")->fetchAll();
        $count = $db->select("SELECT COUNT(`id`) FROM post_brand")->fetch();
        $pages = ceil($count[0] / $prepage);
        require_once BASE_PATH . '/them/admin/pages/brand_posts/index.php';
    }

    public function create()
    {
        $db = new \DataBase();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $item = $db->select("SELECT * FROM items_brands")->fetchAll();
        require_once BASE_PATH . '/them/admin/pages/brand_posts/create.php';
    }

    public function created($req)
    {
        $db = new \DataBase();
        if ($req['img']['tmp_name'] != null) {
            $req['img'] = $this->saveImage($req['img'], 'img');
                if (preg_match('/^[^\x{600}-\x{6FF}]+$/u', str_replace("\\\\", "", $req['slug']))){
                    $db->insert('post_brand', array_keys($req), $req);
                    $this->redirect('admin/brands/post/1');
                }else{
                    flash('msg-post', 'لطفا نوار آدرس را انگلیسی پر کنید');
                    $this->redirectBack();
                }
        } else {
            flash('msg-post', 'لطفا  عکس را وارد کنید');
            $this->redirectBack();
        }

    }

    public function update($id)
    {
        $db = new \DataBase();
        $item = $db->select("SELECT * FROM items_brands")->fetchAll();
        $post = $db->new_select('*', 'post_brand', 'id', $id);
        require_once BASE_PATH . '/them/admin/pages/brand_posts/update.php';
    }

    public function updated($req, $id)
    {
        $db = new \DataBase();
        if ($req['title'] != "") {
            if ($req['img']['tmp_name'] != null) {
                $post = $db->new_select('*', 'post_brand', 'id', $id);
                $this->removeImage($post['img']);
                $req['img'] = $this->saveImage($req['img'], 'img');
            } else {
                unset($req['img']);
            }
            if (preg_match('/^[^\x{600}-\x{6FF}]+$/u', str_replace("\\\\", "", $req['slug']))){
                $db->update('post_brand', $id, array_keys($req), $req);
                $this->redirect('admin/brands/post/1');
            }else{
                flash('msg-post', 'لطفا نوار آدرس را انگلیسی پر کنید');
                $this->redirectBack();
            }
        }else{
            flash('msg-post', 'لطفا عنوان را پر کنید');
            $this->redirectBack();
        }
    }

    public function deleted($id)
    {
        $db = new \DataBase();
        $post = $db->new_select('*', 'post_brand', 'id', $id);
        $this->removeImage($post['img']);
        $db->delete('post_brand', $id);
        $this->redirectBack();
    }
    public function status($id)
    {
        $db = new \DataBase();
        $post = $db->new_select('*', 'post_brand', 'id', $id);
        if($post['status'] == 1)
        {
            $db->update('post_brand',$id,['status'],[0]);
            $this->redirectBack();
        }else{
            $db->update('post_brand',$id,['status'],[1]);
            $this->redirectBack();
        }
        $this->redirectBack(); 
    }
}
