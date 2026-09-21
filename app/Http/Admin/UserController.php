<?php
namespace App\Http\Admin;

class UserController extends AdminController
{
    public function index($page)
    {
        list($page, $offset) = $this->page($page, 10);
        $users = $this->db->fetchAll('SELECT * FROM users ORDER BY id DESC LIMIT ' . $offset . ', 10');
        $total = (int) $this->db->value('SELECT COUNT(*) FROM users');

        return $this->render('them/admin/pages/users/index.php', [
            'users' => $users,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / 10)),
        ]);
    }

    public function role($id)
    {
        $user = $this->db->find('users', (int) $id);
        if ($user) {
            $this->db->updateById('users', (int) $id, ['role' => (int) !$user['role']]);
            flash('success', (int) $user['role'] === 0 ? 'کاربر با موفقیت ادمین شد' : 'دسترسی ادمین از کاربر گرفته شد');
        }
        $this->back('admin/users/1');
    }

    public function writer($id)
    {
        $user = $this->db->find('users', (int) $id);
        if ($user) {
            $this->db->updateById('users', (int) $id, ['writer' => (int) !$user['writer']]);
            flash('success', (int) $user['writer'] === 0 ? 'کاربر با موفقیت نویسنده شد' : 'دسترسی نویسندگی از کاربر گرفته شد');
        }
        $this->back('admin/users/1');
    }

    public function delete($id)
    {
        $this->db->deleteById('users', (int) $id);
        flash('success', 'کاربر با موفقیت حذف شد');
        $this->back('admin/users/1');
    }

    public function impersonate($id)
    {
        $user = $this->db->find('users', (int) $id);
        if (!$user) {
            flash('login_error', 'کاربر یافت نشد');
            $this->back('admin/users/1');
        }

        $this->auth->loginUser($user);
        flash('login', 'ورود به حساب ' . $user['name'] . ' انجام شد');
        $this->redirect('/');
    }
}
