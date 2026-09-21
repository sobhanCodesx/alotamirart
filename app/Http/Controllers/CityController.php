<?php
namespace App\Http\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;
use App\Http\Controller;
use App\Services\SiteContext;
use App\Services\UploadService;

class CityController extends Controller
{
    private $db;

    public function __construct(Request $request, View $view, SiteContext $site, UploadService $uploads, Database $db)
    {
        parent::__construct($request, $view, $site, $uploads);
        $this->db = $db;
    }

    public function index()
    {
        $cities = $this->db->fetchAll('SELECT * FROM cities WHERE status = 1 ORDER BY name ASC');
        return $this->render('them/app/cities/index.php', ['cities' => $cities]);
    }

    public function show($slug)
    {
        $city = $this->cityBySlug($slug);
        if (!$city) {
            http_response_code(404);
            return $this->render('404.php');
        }
        return $this->render('them/app/cities/show.php', ['city' => $city]);
    }

    public function services($city)
    {
        $cityData = $this->cityBySlug($city);
        if (!$cityData) {
            http_response_code(404);
            return $this->render('404.php');
        }
        return $this->render('show-city.php', ['city' => $cityData]);
    }

    public function serviceDetail($service, $city)
    {
        $cityData = $this->cityBySlug($city);
        if (!$cityData) {
            http_response_code(404);
            return $this->render('404.php');
        }

        return $this->render('service-city.php', [
            'city' => $cityData,
            'serviceSlug' => $service,
        ]);
    }

    public function legacyServices()
    {
        $slug = (string) $this->request->query('city', '');
        return $this->services($slug);
    }

    public function legacyService()
    {
        $service = (string) $this->request->query('service', 'refrigerator');
        $city = (string) $this->request->query('city', 'tehran');
        return $this->serviceDetail($service, $city);
    }

    public function washingMachine()
    {
        $city = (string) $this->request->query('city', 'tehran');
        return $this->serviceDetail('washing-machine', $city);
    }

    private function cityBySlug($slug)
    {
        return $this->db->fetch(
            'SELECT * FROM cities WHERE slug = ? AND status = 1 LIMIT 1',
            [$slug]
        );
    }
}
