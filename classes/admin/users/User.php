<?php

class User extends Admin
{
    public function index($page)
    {
        $db = new \DataBase();
      
        ////
        $prepage = 10;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        $users = $db->select("SELECT * FROM users LIMIT {$start},{$prepage}");
        $count = $db->select("SELECT COUNT(`id`) FROM users")->fetch();
        $pages = ceil($count[0] / $prepage);
        require_once BASE_PATH . '/them/admin/pages/users/index.php';
    }

    public function status($id)
    {
        $db = new \DataBase();
        $user = $db->new_select('*','users','id',$id);
        if ($user)
        {
            if ($user['role'] == 0)
            {
                $db->update('users',$id,['role'],['1']);
                $this->redirectBack();
            }else
            {
                $db->update('users',$id,['role'],['0']);
                $this->redirectBack();
            }
        }
    }

    public function delete($id)
    {
        $db = new DataBase();
        $db->delete('users',$id);
        $this->redirectBack();
    }
    public function writer($id)
    {
        $db = new \DataBase();
        $post = $db->new_select('*', 'users', 'id', $id);
        if($post['writer'] == 1)
        {
            $db->update('users',$id,['writer'],[0]);
            $this->redirectBack();
        }else{
            $db->update('users',$id,['writer'],[1]);
            $this->redirectBack();
        }
        $this->redirectBack(); 
    }
}
