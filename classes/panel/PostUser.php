<?php
class PostUser extends Panel
{
    public function index($page){
        $db = new \DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        ////////////////////////////////////////////////////////////////////////
        $prepage = 5;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        $post = $db->select("SELECT * FROM posts WHERE user_id = ? ORDER BY id DESC LIMIT {$start},{$prepage}",$_SESSION['id'])->fetchAll();
        $count = $db->select("SELECT COUNT(`id`) FROM post_brand WHERE user_id = ? ",$_SESSION['id'])->fetch();
        $pages = ceil($count[0] / $prepage);
        require_once BASE_PATH .'/them/panel/posts/index.php';
    }
    public function create(){
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        require_once BASE_PATH .'/them/panel/posts/create.php'; 
    }
    public function created($req){  
        $db = new \DataBase();

        if ($req['img']['tmp_name'] != null) {
            $req['img'] = $this->saveImage($req['img'],'img-');
        }else{
            unset($req['img']);
        }
        $db->insert('posts', array_keys($req), $req);
        $this->redirecte('user/post/1');
    }
    public function update($id){
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $post = $db->new_select('*', 'posts','id',$id);
        require_once BASE_PATH .'/them/panel/posts/update.php'; 
    }
    public function updated($req,$id){
        $db = new DataBase();
        if ($req['img']['tmp_name'] != null) {
            $post = $db->new_select('*', 'posts', 'id', $id);
            $this->removeImage($post['img']);
            $req['img'] = $this->saveImage($req['img'], 'img');
        } else {
            unset($req['img']);
        }
        $db->update('posts', $id, array_keys($req), $req);
        $this->redirecte('user/post/1');
    }
}