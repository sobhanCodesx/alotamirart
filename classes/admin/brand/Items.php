<?php

class Items extends Admin
{
    public function index($page)
    {
        $db = new \DataBase();
      
        ////
        $prepage = 10;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        $item = $db->select("SELECT * FROM items_brands  LIMIT {$start},{$prepage}");
        $count = $db->select("SELECT COUNT(`id`) FROM items_brands")->fetch();
        $pages = ceil($count[0] / $prepage);
        require_once BASE_PATH . '/them/admin/pages/brand_items/index.php';
    }

    public function created($req)
    {
        $db = new \DataBase();
        if ($req['name'] != '') {
            $req['img'] = $this->saveImage($req['img'], 'brand/');

            if ($req['img']) {
                $db->insert('items_brands', array_keys($req), $req);
                $this->redirect('admin/brands/index/1');
            } else {
                $this->redirect('admin/brands/index/1');
            }
        } else {
            $this->redirect('admin/brands/index/1');
        }
    }

    public function update($id)
    {
        $db = new \DataBase();
      
        $item = $db->new_select('*', 'items_brands', 'id', $id);
        require_once BASE_PATH . '/them/admin/pages/brand_items/update.php';
    }

    public function updated($req, $id)
    {
        $db = new \DataBase();
        if ($req['name'] != " ") {
            if ($req['img']['tmp_name'] != null) {
                $item = $db->new_select('*', 'items_brands', 'id', $id);
                $this->removeImage($item['img']);
                $req['img'] = $this->saveImage($req['img'], 'brand/');
            } else {
                unset($req['img']);
            }
            $db->update('items_brands', $id, array_keys($req), $req);
            $this->redirect('admin/brands/index/1');
        }else{
            echo "error";
        }
    }

    public function deleted($id)
    {
        $db = new \DataBase();
        $item = $db->new_select('img', 'items_brands', 'id', $id);
        if ($item) {
            $this->removeImage($item['img']);
            $db->delete('items_brands', $id);
            $this->redirectBack();
        } else {
            $this->redirectBack();
        }
    }
}