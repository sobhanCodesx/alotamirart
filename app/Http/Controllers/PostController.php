<?php
namespace App\Http\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;
use App\Http\Controller;
use App\Services\SiteContext;
use App\Services\UploadService;

class PostController extends Controller
{
    private $db;

    public function __construct(Request $request, View $view, SiteContext $site, UploadService $uploads, Database $db)
    {
        parent::__construct($request, $view, $site, $uploads);
        $this->db = $db;
    }

    public function category($id, $page)
    {
        $id = (int) $id;
        $page = max(1, (int) $page);
        $perPage = 8;
        $offset = ($page - 1) * $perPage;

        $posts = $this->db->fetchAll(
            'SELECT * FROM posts WHERE post_id = ? AND status = 1 ORDER BY created_at DESC LIMIT ' . $offset . ', ' . $perPage,
            [$id]
        );
        $total = (int) $this->db->value(
            'SELECT COUNT(*) FROM posts WHERE post_id = ? AND status = 1',
            [$id]
        );
        $category = $this->db->find('menu', $id);

        return $this->render('them/app/posts/categories.php', [
            'posts' => $posts,
            'post' => $posts,
            'category' => $category,
            'item' => $category,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ], true);
    }

    public function show($id, $slug = null)
    {
        $id = (int) $id;
        $post = $this->db->fetch('SELECT * FROM posts WHERE id = ? AND status = 1 LIMIT 1', [$id]);

        $user = null;
        $view = 0;
        $err = null;

        if ($post) {
            $user = $this->db->fetch('SELECT * FROM users WHERE id = ? LIMIT 1', [(int) $post['user_id']]);
            $this->db->insert('view', ['post_id' => $post['id']]);
            $view = (int) $this->db->value('SELECT COUNT(*) FROM view WHERE post_id = ?', [$post['id']]);
            $err = true;
        } else {
            http_response_code(404);
        }

        $sideBrand = $this->db->fetchAll(
            'SELECT * FROM post_brand WHERE status = 1 ORDER BY created_at DESC LIMIT 6'
        );
        $sidePost = $this->db->fetchAll(
            'SELECT * FROM posts WHERE id <> ? AND status = 1 ORDER BY created_at DESC LIMIT 8',
            [$id]
        );

        return $this->render('them/app/posts/post.php', [
            'post' => $post,
            'user' => $user,
            'view' => $view,
            'err' => $err,
            'side_brand' => $sideBrand,
            'side_post' => $sidePost,
        ], true);
    }
}
