<?php

class Panel
{
    public function __construct()
    {
        $atch = new Auth();
        $atch->checkwriter();
        $this->currentDomain = CURRENT_DOMAIN;
        $this->basePath = BASE_PATH;
    }

    protected function redirecte($url)
    {
        header('Location: ' . trim($this->currentDomain, '/ ') . '/' . trim($url, '/ '));
        exit;
    }

    protected function redirectBacked()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    protected function saveImage($image, $imagePath, $imageName = null)
    {
        if ($imageName) {
            $extension = explode('/', $image['type'])[1];
            $imageName = $imageName . '.' . $extension;
        } else {
            $extension = explode('/', $image['type'])[1];
            $imageName = date("Y-m-d-H-i-s") . '.' . $extension;
        }

        $imageTemp = $image['tmp_name'];
        $imagePath = 'them/admin/dist/img/' . $imagePath;

        if (is_uploaded_file($imageTemp)) {
            if (move_uploaded_file($imageTemp, $imagePath . $imageName)) {
                return $imagePath . $imageName;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    protected function removeImage($path)
    {
        $path = trim($this->basePath, '/ ') . '/' . trim($path, '/ ');
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function indexPanel()
    {
        $db = new DataBase();
        $iUserId = getByUser('id');
        $aUser = $db->select("SELECT * FROM users WHERE id = ?", $iUserId)->fetch();
        require_once BASE_PATH . "/them/panel/index.php";
    }

    public function updateUser($req, $id)
    {
        $db = new DataBase();

        if (!empty($req['name']) and !empty($req['user_name']) and !empty($req['email']) and !empty($req['password'])) {
            $aUserEmail = $db->select('SELECT email FROM users WHERE id = ?', $id)->fetch();
            $aUserUserName = $db->select('SELECT user_name FROM users WHERE id = ?', $id)->fetch();

            $userName = $db->new_select('user_name', 'users', 'user_name', $req['user_name']);
            $user = $db->new_select('email', 'users', 'email', $req['email']);

            if ($aUserEmail[0] == $req['email']) {
                $user = null;
            }
            if ($userName[0] == $req['user_name']) {
                $userName = null;
            }

            if (!empty($user)) {
                flash('msg', 'این ایمیل تکراری می باشد');
                $this->redirectBacked();
                exit();
            }
            if (!empty($userName)) {
                flash('msg', 'نام کاربری در سیستم موجود است لطفا نام کاربری دیگری وارد کنید');
                $this->redirectBacked();
                exite();

            }

            if ($req['img']['tmp_name'] != null) {
                $userimg = $db->new_select('img', 'users', 'id', $id);
                if (!empty($userimg)) $this->removeImage($post['img']);
                $rand = rand(1, 2000000);
                $req['img'] = $this->saveImage($req['img'], $rand . '-userprofile');
            } else {
                unset($req['img']);
            }
            $db->update('users', $id, array_keys($req), $req);
            $_SESSION['img'] = $db->select("SELECT img FROM users WHERE id = ?", $req['id']);
            flash('saveuser', 'اطلاعات بروز شدن برای کارکرد درست یکبار دیگر لاگین کنید');
            $this->redirectBacked();


        } else {
            flash('msg', 'لطفا همه اطلاعات را وارد کنید');
            $this->redirectBacked();
        }
    }
}