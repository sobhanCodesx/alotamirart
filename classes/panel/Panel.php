<?php

class Panel
{
    protected $currentDomain;
    protected $basePath;

    public function __construct()
    {
        $atch = new Auth();
        $atch->checkwriter();
        $this->currentDomain = CURRENT_DOMAIN;
        $this->basePath = BASE_PATH;
    }

    protected function redirecte($url)
    {
        if (!headers_sent()) {
            header('Location: ' . trim($this->currentDomain, '/ ') . '/' . trim($url, '/ '));
            exit;
        } else {
            echo '<script>window.location.href="' . trim($this->currentDomain, '/ ') . '/' . trim($url, '/ ') . '";</script>';
            exit;
        }
    }

    protected function redirectBacked()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            if (!headers_sent()) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                echo '<script>window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
                exit;
            }
        } else {
            $this->redirecte('/');
        }
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
        $dataSeo = $db->getLastInsert('seo');
        $dataHeader = $db->getLastInsert('header');
        $dataFooter = $db->getLastInsert('footer');
        $menu = $db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll();
        require_once BASE_PATH . "/them/panel/index.php";
    }

    // ============================================================
    // ===== متد به‌روزرسانی کاربر (اصلاح شده) =====
    // ============================================================
    public function updateUser($req, $id)
    {
        $db = new DataBase();

        // ===== فقط فیلدهای ضروری رو چک کن (پسورد الزامی نیست) =====
        if (empty($req['name']) || empty($req['user_name']) || empty($req['email']) || empty($req['phon'])) {
            flash('msg', 'لطفا همه اطلاعات را وارد کنید');
            $this->redirectBacked();
            return;
        }

        // ===== دریافت اطلاعات فعلی کاربر =====
        $currentUser = $db->selectOne("SELECT * FROM users WHERE id = ?", [$id]);
        
        if (!$currentUser) {
            flash('msg', 'کاربر یافت نشد');
            $this->redirectBacked();
            return;
        }

        // ===== بررسی تکراری بودن ایمیل (به جز خود کاربر) =====
        $emailExists = $db->selectOne("SELECT email FROM users WHERE email = ? AND id != ?", [$req['email'], $id]);
        if ($emailExists) {
            flash('msg', 'این ایمیل تکراری می باشد');
            $this->redirectBacked();
            return;
        }

        // ===== بررسی تکراری بودن نام کاربری (به جز خود کاربر) =====
        $usernameExists = $db->selectOne("SELECT user_name FROM users WHERE user_name = ? AND id != ?", [$req['user_name'], $id]);
        if ($usernameExists) {
            flash('msg', 'نام کاربری در سیستم موجود است');
            $this->redirectBacked();
            return;
        }

        // ===== ساخت آرایه داده‌ها با حفظ مقادیر قبلی =====
        $data = [
            'name' => $req['name'],
            'user_name' => $req['user_name'],
            'email' => $req['email'],
            'phon' => $req['phon']
        ];

        // ===== مدیریت پسورد (فقط در صورتی که پر شده باشد) =====
        if (!empty($req['password'])) {
            $data['password'] = password_hash($req['password'], PASSWORD_DEFAULT);
        } else {
            // ===== حفظ پسورد قبلی =====
            $data['password'] = $currentUser['password'];
        }

        // ===== مدیریت تصویر =====
        if (isset($req['img']['tmp_name']) && $req['img']['tmp_name'] != null) {
            // حذف تصویر قبلی
            if (!empty($currentUser['img'])) {
                $this->removeImage($currentUser['img']);
            }
            
            $rand = rand(1, 2000000);
            $savedImage = $this->saveImage($req['img'], $rand . '-userprofile');
            if ($savedImage) {
                $data['img'] = $savedImage;
            }
        } else {
            // ===== حفظ تصویر قبلی =====
            if (isset($currentUser['img']) && !empty($currentUser['img'])) {
                $data['img'] = $currentUser['img'];
            }
        }

        // ===== به‌روزرسانی =====
        $result = $db->update('users', $id, array_keys($data), array_values($data));

        if ($result) {
            // ===== به‌روزرسانی session =====
            if (isset($_SESSION['id']) && $_SESSION['id'] == $id) {
                $_SESSION['name'] = $data['name'];
                $_SESSION['user_name'] = $data['user_name'];
                if (isset($data['img'])) {
                    $_SESSION['img'] = $data['img'];
                }
            }
            
            flash('saveuser', 'اطلاعات با موفقیت بروز شد');
        } else {
            flash('msg', 'خطا در به‌روزرسانی اطلاعات');
        }

        $this->redirectBacked();
    }
}