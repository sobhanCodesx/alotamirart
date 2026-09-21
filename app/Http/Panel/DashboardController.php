<?php
namespace App\Http\Panel;

class DashboardController extends PanelController
{
    public function index()
    {
        return $this->render('them/panel/index.php', [
            'aUser' => $this->user,
        ], true);
    }

    public function update($id)
    {
        $id = (int) $id;
        if ($id !== (int) $this->user['id']) $this->redirect('panelcp');

        $data = $this->request->input();
        foreach (['name', 'user_name', 'email', 'phon'] as $field) {
            if (empty($data[$field])) {
                flash('msg', 'لطفا همه اطلاعات را وارد کنید');
                $this->back('panelcp');
            }
        }

        if ($this->db->fetch('SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1', [$data['email'], $id])) {
            flash('msg', 'این ایمیل تکراری می باشد');
            $this->back('panelcp');
        }
        if ($this->db->fetch('SELECT id FROM users WHERE user_name = ? AND id <> ? LIMIT 1', [$data['user_name'], $id])) {
            flash('msg', 'نام کاربری در سیستم موجود است');
            $this->back('panelcp');
        }

        $update = [
            'name' => $data['name'],
            'user_name' => $data['user_name'],
            'email' => $data['email'],
            'phon' => $data['phon'],
        ];

        if (!empty($data['password'])) $update['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $file = $this->request->file('img');
        if ($file && ($saved = $this->uploads->image($file, 'them/admin/dist/img', 'user-'))) {
            if (!empty($this->user['img'])) $this->uploads->remove($this->user['img']);
            $update['img'] = $saved;
        }

        $this->db->updateById('users', $id, $update);
        $this->auth->refreshSession();
        flash('saveuser', 'اطلاعات با موفقیت بروز شد');
        $this->back('panelcp');
    }
}
