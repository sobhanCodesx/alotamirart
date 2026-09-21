<?php

class Cities
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    private function shared()
    {
        return [
            'dataSeo' => $this->db->getLastInsert('seo'),
            'dataHeader' => $this->db->getLastInsert('header'),
            'dataFooter' => $this->db->getLastInsert('footer'),
            'menu' => $this->db->select('SELECT * FROM menu WHERE NOT id = 5 ORDER BY sort')->fetchAll(),
        ];
    }

    private function city($slug)
    {
        return $this->db->select("SELECT * FROM cities WHERE slug = ? AND status = 1", $slug)->fetch();
    }

    public function index()
    {
        extract($this->shared());
        $cities = $this->db->select("SELECT * FROM cities WHERE status = 1 ORDER BY name ASC")->fetchAll();
        if (!$cities) $cities = [];
        require BASE_PATH . '/them/app/cities/index.php';
    }

    public function show($slug)
    {
        extract($this->shared());
        $city = $this->city($slug);
        if (!$city) {
            http_response_code(404);
            require BASE_PATH . '/404.php';
            return;
        }
        require BASE_PATH . '/them/app/cities/show.php';
    }

    public function services($slug)
    {
        extract($this->shared());
        $city = $this->city($slug);
        if (!$city) {
            http_response_code(404);
            require BASE_PATH . '/404.php';
            return;
        }
        require BASE_PATH . '/show-city.php';
    }

    public function serviceDetail($slug)
    {
        extract($this->shared());
        $city = $this->city($slug);
        if (!$city) {
            http_response_code(404);
            require BASE_PATH . '/404.php';
            return;
        }

        $path = trim((string)parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '', PHP_URL_PATH), '/');
        $serviceSlug = 'refrigerator';
        foreach (['washing-machine','air-conditioner','dishwasher','refrigerator','tv','oven'] as $candidate) {
            if (strpos($path, $candidate . '-repair-in-') === 0) {
                $serviceSlug = $candidate;
                break;
            }
        }

        require BASE_PATH . '/service-city.php';
    }
}
