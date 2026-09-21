<?php
namespace App\Services;

use App\Core\Database;
use App\Core\FileCache;

class SiteContext
{
    private $db;
    private $cache;
    private $memory = [];

    public function __construct(Database $db, FileCache $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    public function shared()
    {
        return [
            'dataSeo' => $this->seo(),
            'dataHeader' => $this->header(),
            'dataFooter' => $this->footer(),
            'menu' => $this->menu(),
        ];
    }

    public function seo() { return $this->remember('site.seo', 300, function () { return $this->db->last('seo'); }); }
    public function header() { return $this->remember('site.header', 300, function () { return $this->db->last('header'); }); }
    public function footer() { return $this->remember('site.footer', 300, function () { return $this->db->last('footer'); }); }
    public function menu() { return $this->remember('site.menu', 120, function () { return $this->db->fetchAll('SELECT * FROM menu WHERE id <> ? ORDER BY sort', [5]); }); }

    public function clear()
    {
        $this->memory = [];
        foreach (['site.seo', 'site.header', 'site.footer', 'site.menu'] as $key) $this->cache->forget($key);
    }

    private function remember($key, $ttl, callable $callback)
    {
        if (array_key_exists($key, $this->memory)) return $this->memory[$key];
        $this->memory[$key] = $this->cache->remember($key, $ttl, $callback);
        return $this->memory[$key];
    }
}
