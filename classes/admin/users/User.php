<?php

class User extends Admin
{
    public function index($page)
    {
        $db = new \DataBase();
      
        $prepage = 10;
        $start = ($page > 1) ? ($page * $prepage) - $prepage : 0;
        
        // ===== دریافت کاربران =====
        $users = $db->select("SELECT * FROM users ORDER BY id DESC LIMIT {$start}, {$prepage}");
        
        // ===== دریافت تعداد کل کاربران =====
        $countResult = $db->select("SELECT COUNT(`id`) as total FROM users")->fetch();
        $totalUsers = isset($countResult['total']) ? $countResult['total'] : 0;
        $pages = ($totalUsers > 0) ? ceil($totalUsers / $prepage) : 1;
        
        require_once BASE_PATH . '/them/admin/pages/users/index.php';
    }

    public function status($id)
    {
        $db = new \DataBase();
        $user = $db->new_select('*', 'users', 'id', $id);
        
        if ($user) {
            if ($user['role'] == 0) {
                $db->update('users', $id, ['role'], ['1']);
                flash('success', 'کاربر با موفقیت ادمین شد');
            } else {
                $db->update('users', $id, ['role'], ['0']);
                flash('success', 'دسترسی ادمین از کاربر گرفته شد');
            }
        } else {
            flash('error', 'کاربر یافت نشد');
        }
        
        $this->redirectBack();
    }

    public function delete($id)
    {
        $db = new DataBase();
        $db->delete('users', $id);
        flash('success', 'کاربر با موفقیت حذف شد');
        $this->redirectBack();
    }

    public function writer($id)
    {
        $db = new \DataBase();
        $user = $db->new_select('*', 'users', 'id', $id);
        
        if ($user && isset($user['writer'])) {
            if ($user['writer'] == 1) {
                $db->update('users', $id, ['writer'], [0]);
                flash('success', 'دسترسی نویسندگی از کاربر گرفته شد');
            } else {
                $db->update('users', $id, ['writer'], [1]);
                flash('success', 'کاربر با موفقیت نویسنده شد');
            }
        } else {
            flash('error', 'کاربر یافت نشد');
        }
        
        $this->redirectBack();
    }

    public function innerUser($id)
    {
        $db = new DataBase();
        $user = $db->new_select('*', 'users', 'id', $id);
    
        if ($user != null) {
            unsetUsers();
            
            $_SESSION['id'] = isset($user['id']) ? $user['id'] : 0;
            $_SESSION['role'] = isset($user['role']) ? $user['role'] : 'user';
            $_SESSION['name'] = isset($user['name']) ? $user['name'] : '';
            $_SESSION['w'] = isset($user['writer']) ? $user['writer'] : 0;
            $_SESSION['user_name'] = isset($user['user_name']) ? $user['user_name'] : '';
            
            $sName = $_SESSION['name'];
            flash('login', "خوش آمدی $sName برای رشد کسب کارت آماده باش");
            $this->redirect('/');
        } else {
            flash('login_error', 'کاربری با این مشخصات یافت نشد');
            $this->redirectBack();
        }
    }
}