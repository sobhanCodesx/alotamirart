<?php
session_start();
define('BASE_PATH', __DIR__);
define("CURRENT_DOMAIN", currentdomain() . "/");
define('DISPLAY_ERROR', true);
$dbConfig = require __DIR__ . '/config/database.php';
define('DB_HOST', $dbConfig['legacy_constants']['host']);
define('BD_NAME', $dbConfig['legacy_constants']['name']);
define('DB_USERNAME', $dbConfig['legacy_constants']['username']);
define('DB_PASSWORD', $dbConfig['legacy_constants']['password']);
unset($dbConfig);

/// reqir
require_once './database/DataBase.php';
require_once './router/LegacyRouteMatcher.php';
require_once './classes/admin/index.php';
require_once './classes/admin/cities/AdminCities.php';
require_once './classes/admin/Admin.php';
require_once "./classes/admin/menu/Menu.php";
require_once "./classes/admin/posts/Post.php";
require_once "./classes/admin/users/User.php";
require_once "./classes/admin/brand/Items.php";
require_once "./classes/admin/brand/BrandPosts.php";
require_once "./classes/admin/brand/BrandPosts.php";
require_once "./classes/admin/settings/Seo.php";
require_once "./classes/admin/settings/Header.php";
require_once "./classes/admin/settings/Footer.php";
require_once "./classes/admin/link.php";
require_once './classes/app/Cities.php';
///// app ///
require_once './classes/app/Hom.php';
require_once './classes/app/SiteMap.php';
require_once './classes/app/Brands.php';
require_once './classes/app/Posts.php';
require_once './classes/app/Auth.php';
///// panel ///////////
require_once './classes/panel/Panel.php';
require_once './classes/panel/BrandUser.php';
require_once './classes/panel/PostUser.php';

function limit_words($string, $word_limit)
{
    $words = explode(" ", $string);
    return implode(" ", array_splice($words, 0, $word_limit));
}

function vd($argument)
{
    echo "<pre>";
    var_dump($argument);
}
function get_pag($current_page, $path, $total_pages)
{
    global $page;

    $links = "";
    if ($current_page >= 1 && $current_page <= $total_pages) {
        $links .= "<li class='page-item'>" . "<a class='page-link' href=" . assets($path . '/1') . ">1</a></li>";
        $i = max(2, $current_page - 1);
        if ($i > 2)
            $links .= " ... ";
        for (; $i < min($current_page + 2, $total_pages); $i++) {
            $links .= "<li class='page-item'><a class='page-link' href=" . assets($path . "/" . "$page{$i}") . ">{$i}</a></li>";
        }
        if ($i != $total_pages)
            $links .= " ... ";
        $links .= "<li class='page-item'><a class='page-link' href=" . assets($path . "/" . "$page{$total_pages}") . ">{$total_pages}</a></li>";
    }
    return $links;
}
function checkValue($var,$index)
{
    if (!empty($var[$index]))
    {
        return $var[$index];

    }else{
        return "";
    }
}
function get_pag_cat($current_page, $link, $where, $total_pages)
{
    global $page;

    $links = "";
    if ($current_page >= 1 && $current_page <= $total_pages) {
        $links .= "<li class='page-item'>" . "<a class='page-link' href=" . assets($link . $where . "/" . "1") . ">1</a></li>";
        $i = max(2, $current_page - 1);
        if ($i > 2)
            $links .= " ... ";
        for (; $i < min($current_page + 2, $total_pages); $i++) {
            $links .= "<li class='page-item'><a class='page-link' href=" . assets($link . $where . "/$page{$i}") . ">{$i}</a></li>";
        }
        if ($i != $total_pages)
            $links .= " ... ";
        $links .= "<li class='page-item'><a class='page-link' href=" . assets($link . $where . "/$page{$total_pages}") . ">{$total_pages}</a></li>";
    }
    return $links;
}

function protocol()
{
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
}

function currentdomain()
{
    return protocol() . $_SERVER['HTTP_HOST']; //  http://localhost
}

function assets($src)
{
    $domain = trim(CURRENT_DOMAIN, "/ ") . "/" . trim($src, "/ ");
    return $domain;
}

function methodFild()
{
    return $_SERVER['REQUEST_METHOD'];
}

function DisplayError($err)
{
    if ($err) {

        ini_set('display_error', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_error', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
    }
}

DisplayError(DISPLAY_ERROR);

function currentreq()
{
    return currentdomain() . $_SERVER['REQUEST_URI'];
}
function dd($prams)
{
    echo "<pre>";
    var_dump($prams);
    exit();
}
global $flashMessage;
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}


function flash($name, $value = null)
{
    if ($value === null) {
        global $flashMessage;
        $message = isset($flashMessage[$name]) ? $flashMessage[$name] : '';
        return $message;
    } else {
        $_SESSION['flash_message'][$name] = $value;
    }

}

function uri($path, $class, $method, $methodfild = "GET")
{
    if (strtoupper(methodFild()) !== strtoupper($methodfild)) {
        return false;
    }

    $requestPath = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
    $parameters = LegacyRouteMatcher::match($path, (string)$requestPath);

    if ($parameters === null) {
        return false;
    }

    if (strtoupper($methodfild) === 'POST') {
        $request = !empty($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
        array_unshift($parameters, $request);
    }

    if (!class_exists($class) || !method_exists($class, $method)) {
        http_response_code(500);
        error_log('Invalid route target: ' . $class . '::' . $method);
        echo 'خطای داخلی در مسیر درخواست‌شده.';
        exit();
    }

    $object = new $class;
    if (!is_callable([$object, $method])) {
        http_response_code(500);
        error_log('Uncallable route target: ' . $class . '::' . $method);
        echo 'خطای داخلی در مسیر درخواست‌شده.';
        exit();
    }

    call_user_func_array([$object, $method], $parameters);
    exit();
}

function getByUser($arg)
{
    return isset($_SESSION[$arg]) ? $_SESSION[$arg] : null;
}

function unsetUsers()
{
    unset($_SESSION['name_temp']);
    unset($_SESSION['user_name_temp']);
    unset($_SESSION['email_temp']);
    unset($_SESSION['phon_temp']);
    unset($_SESSION['password_temp']);
}
require_once './router/admin.php';
require_once './router/panel.php';
require_once './router/them.php';
require_once '404.php';
