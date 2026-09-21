<?php
namespace App\Http\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;
use App\Http\Controller;
use App\Services\SiteContext;
use App\Services\UploadService;

class BrandController extends Controller
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

        $brands = $this->db->fetchAll(
            'SELECT * FROM post_brand WHERE brand_id = ? AND status = 1 ORDER BY updated_at DESC LIMIT ' . $offset . ', ' . $perPage,
            [$id]
        );
        $total = (int) $this->db->value(
            'SELECT COUNT(*) FROM post_brand WHERE brand_id = ? AND status = 1',
            [$id]
        );
        $item = $this->db->find('items_brands', $id);

        return $this->render('them/app/brands/categories.php', [
            'brands' => $brands,
            'item' => $item,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ], true);
    }

    public function show($slug, $id)
    {
        $id = (int) $id;
        $post = $this->db->fetch(
            'SELECT pb.*, (SELECT name FROM items_brands ib WHERE ib.id = pb.brand_id) AS namebrand
             FROM post_brand pb WHERE pb.slug = ? AND pb.id = ? AND pb.status = 1 LIMIT 1',
            [$slug, $id]
        );

        $user = null;
        $view = 0;
        $err = null;

        if ($post) {
            if (!empty($post['user_id'])) {
                $user = $this->db->fetch('SELECT * FROM users WHERE id = ? LIMIT 1', [(int) $post['user_id']]);
            }
            $this->db->insert('view_brand', ['post_id' => $post['id']]);
            $view = (int) $this->db->value('SELECT COUNT(*) FROM view_brand WHERE post_id = ?', [$post['id']]);
            $err = true;
        } else {
            http_response_code(404);
        }

        return $this->render('them/app/brands/post.php', [
            'post' => $post,
            'user' => $user,
            'view' => $view,
            'err' => $err,
            'side_brand' => $this->db->fetchAll('SELECT * FROM post_brand WHERE status = 1 ORDER BY updated_at DESC LIMIT 6'),
            'side_post' => $this->db->fetchAll('SELECT * FROM posts WHERE id <> ? AND status = 1 ORDER BY updated_at DESC LIMIT 8', [$id]),
        ], true);
    }
}
