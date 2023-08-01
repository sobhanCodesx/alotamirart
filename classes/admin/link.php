<?php
class Link extends Admin
{
    public function index($page)
    {
        $db = new \DataBase();
        $ostan = $db->select("SELECT * FROM provinces");
        ////
        $prepage = 10;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        $link = $db->select("SELECT * FROM link LIMIT {$start},{$prepage}");
        $count = $db->select("SELECT COUNT(`id`) FROM link")->fetch();
        $pages = ceil($count[0] / $prepage);
        require_once BASE_PATH . '/them/admin/pages/link.php';
    }
    public function create($req)
    {
        $db = new \DataBase();
        if(!$req['title'] == "" and !$req['link'] == "")
        {
            $db->insert('link', array_keys($req), $req);
            $this->redirect('backlink/admin/1');
        }else{
            $this->redirectBack();
        }
    }
    public function delete($id)
    {
        $db = new DataBase();
        $db->delete('link',$id);
        $this->redirectBack();
    }
}