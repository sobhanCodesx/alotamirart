<?php
namespace App\Http\Panel;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;
use App\Http\Controller;
use App\Services\AuthService;
use App\Services\SiteContext;
use App\Services\UploadService;

abstract class PanelController extends Controller
{
    protected $db;
    protected $auth;
    protected $user;

    public function __construct(Request $request, View $view, SiteContext $site, UploadService $uploads, Database $db, AuthService $auth)
    {
        parent::__construct($request, $view, $site, $uploads);
        $this->db = $db;
        $this->auth = $auth;
        $this->user = $this->auth->requireWriter();
    }

    protected function page($page, $perPage)
    {
        $page = max(1, (int) $page);
        return [$page, ($page - 1) * $perPage];
    }

    protected function slug($value)
    {
        $value = trim((string) $value);
        $value = preg_replace('/\s+/u', '-', $value);
        $value = preg_replace('/[^\pL\pN\-]+/u', '', $value);
        $value = function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
        return trim($value, '-');
    }
}
