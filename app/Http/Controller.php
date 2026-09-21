<?php
namespace App\Http;

use App\Core\Request;
use App\Core\View;
use App\Services\SiteContext;
use App\Services\UploadService;

abstract class Controller
{
    protected $request;
    protected $view;
    protected $site;
    protected $uploads;

    public function __construct(Request $request, View $view, SiteContext $site, UploadService $uploads)
    {
        $this->request = $request;
        $this->view = $view;
        $this->site = $site;
        $this->uploads = $uploads;
    }

    protected function render($path, array $data = [], $shared = false)
    {
        if ($shared) $data = array_merge($this->site->shared(), $data);
        return $this->view->render($path, $data);
    }

    protected function redirect($url) { redirect($url); }

    protected function back($fallback = '/')
    {
        $referer = $this->request->server('HTTP_REFERER');
        redirect($referer ?: $fallback);
    }
}
