<?php

class BrandUser extends Panel
{
    public function index($page)
    {
        $db = new \DataBase();
       $dataSeo = $db->getLastInsert('seo');
       $dataHeader = $db->getLastInsert('header');
       $dataFooter = $db->getLastInsert('footer');

        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        ////////////////////////////////////////////////////////////////////////
        $prepage = 5;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        $post = $db->select("SELECT * FROM post_brand WHERE user_id = ? ORDER BY id DESC LIMIT {$start},{$prepage}", $_SESSION['id'])->fetchAll();
        $count = $db->select("SELECT COUNT(`id`) FROM post_brand WHERE user_id = ?", $_SESSION['id'])->fetch();
        $pages = ceil($count[0] / $prepage);
        require_once BASE_PATH . '/them/panel/brand/index.php';
    }

    public function create()
    {
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
       $dataFooter = $db->getLastInsert('footer');
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        $item = $db->select("SELECT * FROM items_brands")->fetchAll();
        require_once BASE_PATH . '/them/panel/brand/create.php';
    }

    public function created($req)
    {
        $db = new \DataBase();
       $dataSeo = $db->getLastInsert('seo');
$dataHeader = $db->getLastInsert('header');
$dataFooter = $db->getLastInsert('footer');
        if ($req['img']['tmp_name'] != null) {
            $req['img'] = $this->saveImage($req['img'], 'img');
                if (preg_match('/^[^\x{600}-\x{6FF}]+$/u', str_replace("\\\\", "", $req['slug']))){
                    $db->insert('post_brand', array_keys($req), $req);
                    $this->redirecte('user/brand/1');
                }else{
                    flash('msg-post', 'لطفا نوار آدرس را انگلیسی پر کنید');
                    $this->redirectBacked();
                }
        } else {
            flash('msg-post', 'لطفا  عکس را وارد کنید');
            $this->redirectBacked();
        }
       
    }

    public function update($id)
    {
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        $item = $db->select("SELECT * FROM items_brands")->fetchAll();
        $post = $db->new_select('*', 'post_brand ', 'id', $id);
        require_once BASE_PATH . '/them/panel/brand/update.php';
    }

    public function updated($req, $id)
    {
        $db = new DataBase();
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
                $this->redirecte('user/brand/1');
            }else{
                flash('msg-post', 'لطفا نوار آدرس را انگلیسی پر کنید');
                $this->redirectBacked();
            }
        }else{
            flash('msg-post', 'لطفا عنوان را پر کنید');
            $this->redirectBacked();
        }
        
    }
}