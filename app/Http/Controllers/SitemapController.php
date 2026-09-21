<?php
namespace App\Http\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;
use App\Http\Controller;
use App\Services\SiteContext;
use App\Services\UploadService;

class SitemapController extends Controller
{
    private $db;

    public function __construct(Request $request, View $view, SiteContext $site, UploadService $uploads, Database $db)
    {
        parent::__construct($request, $view, $site, $uploads);
        $this->db = $db;
    }

    public function post()
    {
        header('Content-Type: application/xml; charset=UTF-8');
        return $this->render('mappost/sitemap.php', [
            'get' => $this->db->fetchAll('SELECT * FROM posts WHERE status = 1'),
        ]);
    }

    public function brand()
    {
        header('Content-Type: application/xml; charset=UTF-8');
        return $this->render('mapbrand/sitemap.php', [
            'get' => $this->db->fetchAll('SELECT * FROM post_brand WHERE status = 1'),
        ]);
    }
}
