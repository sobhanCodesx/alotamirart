<?php

class Auth
{
    private function redirect($url)
    {
        header('Location: ' . trim(CURRENT_DOMAIN, '/ ') . '/' . trim($url, '/ '));
        exit;
    }

    private function redirectBack()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function register()
    {
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        if(isset($_SESSION['name'])){
            $this->redirect('/');
        }else{
            require_once BASE_PATH . '/them/app/Auth/register.php';
        }
    }

    public function registered($req)
    {
        $db = new DataBase();

        $_SESSION['name_temp'] = $req['name'];
        $_SESSION['user_name_temp'] = $req['user_name'];
        $_SESSION['email_temp'] = $req['email'];
        $_SESSION['phon_temp'] = $req['phon'];
        $_SESSION['password_temp'] = $req['password'];

        if ($req['password_two'] != $req['password']) {
            flash('msg', 'لطفا پسورد یکسان وارد کنین');
            $this->redirectBack();
        } else {
            $user = $db->new_select('email', 'users', 'email', $req['email']);
            $userName = $db->new_select('user_name', 'users', 'user_name', $req['user_name']);
            if(!empty($userName))
            {
                flash('msg', 'نام کاربری در سیستم موجود است لطفا نام کاربری دیگری وارد کنید');
                $this->redirectBack();
                exit();
            }
            if (!empty($user)) {
                flash('msg', 'این ایمیل تکراری می باشد');
                $this->redirectBack();
            } else {
                $req['password_two'] = "active";
                $db->insert('users', array_keys($req), $req);
                flash('saveuser', 'اطلاعات با موفقیت ثبت شد');
                unsetUsers();
                $this->redirect('login');
            }
        }
    }

    public function login()
    {
        $db = new DataBase();
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        if(isset($_SESSION['name'])){
            $this->redirect('/');
        }else{
            require_once BASE_PATH . '/them/app/Auth/login.php';
        }
    }

    public function logined($request)
    {
        $db = new DataBase();
        if (empty($request['user_name']) || empty($request['password'])) {
            flash('login_error', 'تمامی فیلد ها الزامی میباشند');
            $this->redirectBack();
        } else {
            $aReqLog = [ $request['user_name'] , $request['password'] ];

            $user = $db->all("SELECT * FROM users WHERE user_name = ? AND password = ?", $aReqLog);

            if (!empty($user)) {
                unsetUsers();

                // 👈 اگر all() آرایه‌ای از ردیف‌ها برمی‌گرداند، اولین ردیف را برمی‌داریم
                if (isset($user[0]) && is_array($user[0])) {
                    $user = $user[0];
                }

                $_SESSION['id']        = $user['id'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['name']      = $user['name'];
                $_SESSION['w']         = $user['writer'];
                $_SESSION['writer']    = $user['writer'];   // 👈 هماهنگ‌سازی با فایل‌های دیگر
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['img']       = isset($user['img']) ? $user['img'] : '';

                $sName = $_SESSION['name'];
                flash('login', "خوش آمدی $sName برای رشد کسب کارت آماده باش");
                $this->redirect('/');
            } else {
                flash('login_error', 'کاربری با این مشخصات یافت نشد');
                $this->redirectBack();
            }
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        $this->redirect('/');
    }

    public function checkAdmin()
    {
        $db = new DataBase();
        if(!isset($_SESSION['id']))
        {
            $this->redirect('/');
        }else{
            $user = $db->select("SELECT * FROM users WHERE id = ?", $_SESSION['id'])->fetch();
            if($user){
                if($user['role'] == 0){
                    $this->redirect('/');
                }
            }else{
                $this->redirect('/');
            }
        }
    }

    public function checkwriter()
    {
        $db = new DataBase();
        if(isset($_SESSION['id']))
        {
            if(isset($_SESSION['w']) && $_SESSION['w'] == 0){
                $this->redirect('/');
            }
        }else{
            $this->redirect('/');
        }
    }
}