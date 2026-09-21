<?php

class Post extends Admin
{
    public function index($page)
    {
        $db = new \DataBase();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();

        $prepage = 20;
        $page = max(1, (int) $page);
        $start = ($page - 1) * $prepage;

        $post = $db->select(
            "SELECT *,(SELECT name FROM users WHERE users.id = posts.user_id) AS w
             FROM posts ORDER BY created_at DESC LIMIT {$start},{$prepage}"
        )->fetchAll();

        $countResult = $db->select("SELECT COUNT(`id`) AS total FROM posts")->fetch();
        $totalPosts = isset($countResult['total']) ? (int) $countResult['total'] : 0;
        $pages = max(1, (int) ceil($totalPosts / $prepage));

        require_once BASE_PATH . '/them/admin/pages/posts/index.php';
    }

    public function create($req = null)
    {
        $db = new \DataBase();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        require_once BASE_PATH . '/them/admin/pages/posts/create.php';
    }

    public function created($req)
    {
        $db = new \DataBase();

        if (!isset($req['user_id']) && isset($_SESSION['id'])) {
            $req['user_id'] = (int) $_SESSION['id'];
        }

        $newImage = null;
        if ($this->hasUpload($req, 'img')) {
            $newImage = $this->saveImage($req['img'], 'img-');
            if (!$newImage) {
                flash('error', 'آپلود تصویر شاخص انجام نشد');
                $this->redirectBack('admin/posts/create');
            }
            $req['img'] = $newImage;
        } else {
            unset($req['img']);
        }

        if (!empty($req['title'])) {
            $req['slug'] = $this->createSlug($req['title']);
        }

        unset($req['_action']);
        $req = $this->filterPostPayload($db, $req);

        if (empty($req)) {
            if ($newImage) $this->removeImage($newImage);
            flash('error', 'اطلاعات معتبری برای ذخیره مقاله ارسال نشده است');
            $this->redirectBack('admin/posts/create');
        }

        $id = $db->insert('posts', array_keys($req), array_values($req));
        if ($id === false) {
            if ($newImage) $this->removeImage($newImage);
            flash('error', 'ذخیره مقاله انجام نشد. اطلاعات فرم و ساختار دیتابیس را بررسی کنید.');
            $this->redirectBack('admin/posts/create');
        }

        flash('success', 'پست با موفقیت ایجاد شد');
        $this->redirect('admin/posts/index/1');
    }

    public function update($id)
    {
        $db = new \DataBase();
        $menus = $db->select("SELECT * FROM menu")->fetchAll();
        $post = $db->new_select('*', 'posts', 'id', $id);

        if (!$post) {
            flash('error', 'پست یافت نشد');
            $this->redirect('admin/posts/index/1');
        }

        require_once BASE_PATH . '/them/admin/pages/posts/update.php';
    }

    public function updated($req, $id)
    {
        $db = new \DataBase();
        $currentPost = $db->new_select('*', 'posts', 'id', $id);

        if (!$currentPost) {
            flash('error', 'پست یافت نشد');
            $this->redirect('admin/posts/index/1');
        }

        $newImage = null;
        if ($this->hasUpload($req, 'img')) {
            $newImage = $this->saveImage($req['img'], 'img-');
            if (!$newImage) {
                flash('error', 'آپلود تصویر جدید انجام نشد و تصویر قبلی حفظ شد');
                $this->redirectBack('admin/posts/update/' . (int) $id);
            }
            $req['img'] = $newImage;
        } else {
            unset($req['img']);
        }

        if (!empty($req['title'])) {
            $req['slug'] = $this->createSlug($req['title']);
        }

        unset($req['_action']);
        $req = $this->filterPostPayload($db, $req);

        if (empty($req)) {
            if ($newImage) $this->removeImage($newImage);
            flash('error', 'اطلاعات معتبری برای بروزرسانی ارسال نشده است');
            $this->redirectBack('admin/posts/update/' . (int) $id);
        }

        $result = $db->update('posts', $id, array_keys($req), array_values($req));

        if (!$result) {
            if ($newImage) $this->removeImage($newImage);
            flash('error', 'بروزرسانی مقاله انجام نشد و اطلاعات قبلی حفظ شد');
            $this->redirectBack('admin/posts/update/' . (int) $id);
        }

        if ($newImage && !empty($currentPost['img']) && $currentPost['img'] !== $newImage) {
            $this->removeImage($currentPost['img']);
        }

        flash('success', 'پست با موفقیت به‌روزرسانی شد');
        $this->redirect('admin/posts/index/1');
    }

    public function deleted($id)
    {
        $db = new \DataBase();
        $post = $db->new_select('*', 'posts', 'id', $id);

        if (!$post) {
            flash('error', 'پست یافت نشد');
            $this->redirectBack('admin/posts/index/1');
        }

        if ($db->delete('posts', $id)) {
            if (!empty($post['img'])) {
                $this->removeImage($post['img']);
            }
            flash('success', 'پست با موفقیت حذف شد');
        } else {
            flash('error', 'حذف پست انجام نشد');
        }

        $this->redirectBack('admin/posts/index/1');
    }

    public function status($id)
    {
        $db = new \DataBase();
        $post = $db->new_select('*', 'posts', 'id', $id);

        if (!$post) {
            flash('error', 'پست یافت نشد');
            $this->redirectBack('admin/posts/index/1');
        }

        $newStatus = !empty($post['status']) ? 0 : 1;
        if ($db->update('posts', $id, ['status'], [$newStatus])) {
            flash('success', $newStatus ? 'پست فعال شد' : 'پست غیرفعال شد');
        } else {
            flash('error', 'تغییر وضعیت پست انجام نشد');
        }

        $this->redirectBack('admin/posts/index/1');
    }

    public function uploadImageAjax($req)
    {
        header('Content-Type: application/json; charset=UTF-8');

        if (!$this->hasUpload($req, 'upload')) {
            echo json_encode(['success' => false, 'error' => 'فایلی برای آپلود دریافت نشد'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $saved = $this->saveImage($req['upload'], 'editor-', uniqid('content-', true));
        if (!$saved) {
            echo json_encode(['success' => false, 'error' => 'ذخیره تصویر انجام نشد'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $url = assets($saved);
        $width = max(50, (int) (isset($req['width']) ? $req['width'] : 800));
        $height = max(50, (int) (isset($req['height']) ? $req['height'] : 600));
        $alt = isset($req['alt']) ? trim($req['alt']) : '';
        $align = isset($req['align']) ? $req['align'] : 'center';
        $link = isset($req['link']) ? trim($req['link']) : '';

        $style = 'max-width:100%;height:auto;';
        if ($align === 'center') $style .= 'display:block;margin-left:auto;margin-right:auto;';
        if ($align === 'right') $style .= 'float:right;margin:0 0 15px 15px;';
        if ($align === 'left') $style .= 'float:left;margin:0 15px 15px 0;';

        $html = '<img src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"'
            . ' alt="' . htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') . '"'
            . ' width="' . $width . '" height="' . $height . '"'
            . ' style="' . $style . '">';

        if ($link !== '') {
            $html = '<a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">' . $html . '</a>';
        }

        echo json_encode([
            'success' => true,
            'url' => $url,
            'html' => $html,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function uploadImageCkeditor($req)
    {
        $funcNum = isset($_GET['CKEditorFuncNum']) ? (int) $_GET['CKEditorFuncNum'] : 0;
        $url = '';
        $message = '';

        if ($this->hasUpload($req, 'upload')) {
            $saved = $this->saveImage($req['upload'], 'editor-', uniqid('ckeditor-', true));
            if ($saved) {
                $url = assets($saved);
            } else {
                $message = 'ذخیره تصویر انجام نشد';
            }
        } else {
            $message = 'فایلی دریافت نشد';
        }

        header('Content-Type: text/html; charset=UTF-8');
        echo '<script>window.parent.CKEDITOR.tools.callFunction('
            . json_encode($funcNum) . ','
            . json_encode($url, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ','
            . json_encode($message, JSON_UNESCAPED_UNICODE)
            . ');</script>';
        exit;
    }

    protected function createSlug($title)
    {
        $slug = trim($title);
        $slug = preg_replace('/\s+/u', '-', $slug);
        $slug = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $slug);
        return trim(strtolower($slug), '-');
    }

    private function hasUpload(array $req, $key)
    {
        return isset($req[$key])
            && is_array($req[$key])
            && !empty($req[$key]['tmp_name'])
            && (!isset($req[$key]['error']) || (int) $req[$key]['error'] === UPLOAD_ERR_OK);
    }

    private function filterPostPayload(DataBase $db, array $req)
    {
        $allowed = [];
        $stmt = $db->select('SHOW COLUMNS FROM posts');

        if ($stmt) {
            foreach ($stmt->fetchAll() as $column) {
                if (isset($column['Field'])) {
                    $allowed[$column['Field']] = true;
                }
            }
        }

        if (empty($allowed)) {
            foreach (['title', 'description', 'user_id', 'img', 'content', 'tags', 'post_id', 'status', 'slug', 'contact_number', 'keyword'] as $field) {
                $allowed[$field] = true;
            }
        }

        $filtered = [];
        foreach ($req as $key => $value) {
            if (isset($allowed[$key]) && !is_array($value)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }
}
