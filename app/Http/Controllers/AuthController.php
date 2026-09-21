<?php
namespace App\Http\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;
use App\Http\Controller;
use App\Services\AuthService;
use App\Services\SiteContext;
use App\Services\UploadService;

class AuthController extends Controller
{
    private $db;
    private $auth;

    public function __construct(Request $request, View $view, SiteContext $site, UploadService $uploads, Database $db, AuthService $auth)
    {
        parent::__construct($request, $view, $site, $uploads);
        $this->db = $db;
        $this->auth = $auth;
    }

    public function register()
    {
        if ($this->auth->user()) $this->redirect('/');
        return $this->render('them/app/Auth/register.php', [], true);
    }

    public function registered()
    {
        $req = $this->request->input();
        foreach (['name', 'user_name', 'email', 'phon', 'password'] as $field) {
            $_SESSION[$field . '_temp'] = isset($req[$field]) ? $req[$field] : '';
        }

        if (empty($req['name']) || empty($req['user_name']) || empty($req['email']) || empty($req['password'])) {
            flash('msg', 'تمامی فیلدهای ضروری را وارد کنید');
            $this->back('register');
        }

        if (!isset($req['password_two']) || $req['password_two'] !== $req['password']) {
            flash('msg', 'لطفا پسورد یکسان وارد کنین');
            $this->back('register');
        }

        if ($this->db->fetch('SELECT id FROM users WHERE user_name = ? LIMIT 1', [$req['user_name']])) {
            flash('msg', 'نام کاربری در سیستم موجود است لطفا نام کاربری دیگری وارد کنید');
            $this->back('register');
        }

        if ($this->db->fetch('SELECT id FROM users WHERE email = ? LIMIT 1', [$req['email']])) {
            flash('msg', 'این ایمیل تکراری می باشد');
            $this->back('register');
        }

        $this->db->insert('users', [
            'name' => trim($req['name']),
            'user_name' => trim($req['user_name']),
            'email' => trim($req['email']),
            'phon' => isset($req['phon']) ? trim($req['phon']) : '',
            'password' => password_hash($req['password'], PASSWORD_DEFAULT),
            'password_two' => 'active',
        ]);

        flash('saveuser', 'اطلاعات با موفقیت ثبت شد');
        unsetUsers();
        $this->redirect('login');
    }

    public function login()
    {
        if ($this->auth->user()) $this->redirect('/');
        return $this->render('them/app/Auth/login.php', [], true);
    }

    public function logined()
    {
        $username = trim((string) $this->request->input('user_name', ''));
        $password = (string) $this->request->input('password', '');

        if ($username === '' || $password === '') {
            flash('login_error', 'تمامی فیلد ها الزامی میباشند');
            $this->back('login');
        }

        if (!$this->auth->attempt($username, $password)) {
            flash('login_error', 'کاربری با این مشخصات یافت نشد');
            $this->back('login');
        }

        $name = isset($_SESSION['name']) ? $_SESSION['name'] : '';
        flash('login', 'خوش آمدی ' . $name . ' برای رشد کسب کارت آماده باش');
        $this->redirect('/');
    }

    public function logout()
    {
        $this->auth->logout();
        $this->redirect('/');
    }
}
