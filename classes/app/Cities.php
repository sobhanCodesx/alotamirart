<?php

class Cities
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    // ===== لیست همه شهرها =====
    public function index()
    {
        $cities = $this->db->select("SELECT * FROM cities WHERE status = 1 ORDER BY name ASC")->fetchAll();
        
        // ===== اگر شهری وجود نداشت =====
        if (!$cities) {
            $cities = [];
        }
        
        // ===== شامل کردن فایل ویو =====
        $viewPath = BASE_PATH . '/them/app/cities/index.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // ===== نمایش مستقیم لیست شهرها اگر فایل ویو وجود نداشت =====
            echo "<!DOCTYPE html><html dir='rtl'><head><meta charset='UTF-8'><title>شهرهای تحت پوشش</title>";
            echo "<style>body{font-family:Tahoma;padding:20px;background:#f0f0f0;direction:rtl;}";
            echo ".box{max-width:1000px;margin:0 auto;background:#fff;padding:30px;border-radius:16px;}";
            echo "h1{text-align:center;color:#0f172a;border-bottom:3px solid #facc15;padding-bottom:15px;}";
            echo ".city{display:inline-block;background:#f8fafc;border:2px solid #e9edf2;padding:15px 25px;margin:8px;border-radius:12px;}";
            echo ".city a{text-decoration:none;color:#1a1a2e;font-weight:700;}";
            echo ".city a:hover{color:#facc15;}</style></head><body>";
            echo "<div class='box'><h1>🏙️ شهرهای تحت پوشش</h1>";
            echo "<div style='text-align:center;'>";
            if (!empty($cities)) {
                foreach ($cities as $city) {
                    echo "<div class='city'><a href='/city/" . $city['slug'] . "'>🏙️ " . $city['name'] . "</a></div>";
                }
            } else {
                echo "<p style='text-align:center;color:#999;'>هیچ شهری یافت نشد</p>";
            }
            echo "</div></div></body></html>";
        }
    }

    // ===== نمایش یک شهر =====
    public function show($slug)
    {
        $city = $this->db->select("SELECT * FROM cities WHERE slug = ? AND status = 1", $slug)->fetch();
        
        if (!$city) {
            http_response_code(404);
            echo "شهر مورد نظر یافت نشد";
            return;
        }
        
        $viewPath = BASE_PATH . '/them/app/cities/show.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "<h1>🏙️ " . $city['name'] . "</h1>";
            echo "<p>شهر " . $city['name'] . " در سیستم ثبت شده است.</p>";
        }
    }
}