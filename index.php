<?php
session_start();
define('BASE_PATH', __DIR__);
define("CURRENT_DOMAIN", currentdomain() . "/");
define('DISPLAY_ERROR', true);
define('DB_HOST', 'localhost');
define('BD_NAME', 'mbziliwc_danesh');
define('DB_USERNAME', 'mbziliwc_danesh');
define('DB_PASSWORD', 'fV7+Qjy[RU5S');

/// reqir
require_once './database/DataBase.php';
require_once './classes/admin/index.php';
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
    // requset uri
    $requri = explode("?", currentreq())[0];
    $requri = str_replace(CURRENT_DOMAIN, "", $requri);
    $requri = trim($requri, "/ ");
    $Array_request = explode("/", $requri);
    $Array_request = array_filter($Array_request);
    // reser uri
    $path = trim($path, "/ ");
    $pathes = explode("/", $path);
    $Array_reserved = array_filter($pathes);
    // compair uri req with reserved uri

    if (sizeof($Array_request) != sizeof($Array_reserved) || methodFild() != $methodfild) {
        return false;
    }
    $prameter = [];
    for ($i = 0; $i < sizeof($Array_request); $i++) {
        if ($Array_reserved[$i][0] == "{" and $Array_reserved[$i][strlen($Array_reserved[$i]) - 1] == "}") {
            array_push($prameter, $Array_request[$i]);
        } elseif ($Array_request[$i] !== $Array_reserved[$i]) {
            return false;
        }
    }
    if (methodFild() == "POST") {
        $req = isset($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
        $prameter = array_merge([$req], $prameter);
    }
    $object = new $class;
    call_user_func_array(array($object, $method), $prameter);
    exit();
}

function getByUser($arg)
{
    return $_SESSION[$arg];
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
