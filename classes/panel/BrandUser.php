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
        $count = $db->select("SELECT COUNT(`id`) AS total FROM post_brand WHERE user_id = ?", $_SESSION['id'])->fetch();
        $total = isset($count['total']) ? (int)$count['total'] : 0;
        $pages = max(1, (int)ceil($total / $prepage));
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
        $req['user_id'] = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;
        if (isset($req['img']) && is_array($req['img']) && !empty($req['img']['tmp_name'])) {
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
        $post = $db->new_select('*', 'post_brand', 'id', $id);
        if (!$post) {
            flash('msg-post', 'محتوا یافت نشد');
            $this->redirecte('user/brand/1');
            return;
        }
        require_once BASE_PATH . '/them/panel/brand/update.php';
    }

    public function updated($req, $id)
    {
        $db = new DataBase();
        if ($req['title'] != "") {
            if (isset($req['img']) && is_array($req['img']) && !empty($req['img']['tmp_name'])) {
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