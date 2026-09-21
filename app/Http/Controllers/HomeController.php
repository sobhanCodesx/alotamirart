<?php
namespace App\Http\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;
use App\Http\Controller;
use App\Services\SiteContext;
use App\Services\UploadService;

class HomeController extends Controller
{
    private $db;

    public function __construct(Request $request, View $view, SiteContext $site, UploadService $uploads, Database $db)
    {
        parent::__construct($request, $view, $site, $uploads);
        $this->db = $db;
    }

    public function index()
    {
        $data = [
            'brand' => $this->db->fetchAll('SELECT * FROM items_brands ORDER BY id DESC LIMIT 12'),
            'post' => $this->db->fetchAll('SELECT * FROM posts WHERE status = 1 ORDER BY created_at DESC LIMIT 8'),
            'brands' => $this->db->fetchAll('SELECT * FROM post_brand WHERE status = 1 ORDER BY created_at DESC LIMIT 8'),
        ];

        return $this->render('them/app/index.php', $data, true);
    }

    public function search($page)
    {
        $page = max(1, (int) $page);
        $term = trim((string) $this->request->input('search', ''));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $like = '%' . $term . '%';

        $post = [];
        $totalPosts = 0;
        if ($term !== '') {
            $post = $this->db->fetchAll(
                'SELECT * FROM post_brand WHERE title LIKE ? AND status = 1 ORDER BY created_at DESC LIMIT ' . $offset . ', ' . $perPage,
                [$like]
            );
            $totalPosts = (int) $this->db->value(
                'SELECT COUNT(*) FROM post_brand WHERE title LIKE ? AND status = 1',
                [$like]
            );
        }

        return $this->render('them/app/search.php', [
            'post' => $post,
            'result' => $term,
            'err' => !empty($post) ? true : null,
            'page' => $page,
            'pages' => max(1, (int) ceil($totalPosts / $perPage)),
        ], true);
    }

    public function profile($name, $id)
    {
        $id = (int) $id;
        $user = $this->db->fetch(
            'SELECT * FROM users WHERE user_name = ? AND id = ? LIMIT 1',
            [$name, $id]
        );

        if (!$user) {
            $this->redirect('/');
        }

        if (empty($user['img'])) {
            $user['img'] = 'them/admin/dist/img/avatar.png';
        }

        $posts = $this->db->fetchAll(
            'SELECT id, title, user_id, description, content, img, (SELECT COUNT(*) FROM view v WHERE v.post_id = posts.id) AS view
             FROM posts WHERE user_id = ? AND status = 1 ORDER BY created_at DESC LIMIT 6',
            [$id]
        );

        $brand = $this->db->fetchAll(
            'SELECT id, title, content, brand_id, des, img, slug, (SELECT COUNT(*) FROM view_brand vb WHERE vb.post_id = post_brand.id) AS view
             FROM post_brand WHERE user_id = ? AND status = 1 ORDER BY created_at DESC LIMIT 6',
            [$id]
        );

        return $this->render('them/app/profile/index.php', [
            'posts' => $posts,
            'brand' => $brand,
            'user' => $user,
        ], true);
    }

    public function menu($slug)
    {
        $category = $this->db->fetch('SELECT * FROM menu WHERE slug = ? LIMIT 1', [$slug]);
        if (!$category) {
            http_response_code(404);
            return $this->render('404.php');
        }

        $posts = $this->db->fetchAll(
            'SELECT * FROM posts WHERE post_id = ? AND status = 1 ORDER BY created_at DESC',
            [$category['id']]
        );

        $view = is_file(BASE_PATH . '/them/app/menu.php')
            ? 'them/app/menu.php'
            : 'them/app/posts/categories.php';

        return $this->render($view, [
            'posts' => $posts,
            'post' => $posts,
            'category' => $category,
            'item' => $category,
            'page' => 1,
            'pages' => 1,
        ], true);
    }
}
