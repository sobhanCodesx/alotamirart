<?php
if (!function_exists('protocol')) {
    function protocol()
    {
        $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $forwarded = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
        return ($https || $forwarded) ? 'https://' : 'http://';
    }
}

if (!function_exists('currentdomain')) {
    function currentdomain()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        return protocol() . $host;
    }
}

if (!function_exists('assets')) {
    function assets($src = '') { return rtrim(currentdomain(), '/') . '/' . ltrim($src, '/'); }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        $target = preg_match('#^https?://#i', (string) $url) ? $url : assets($url);
        header('Location: ' . $target, true, 302);
        exit;
    }
}

if (!function_exists('flash')) {
    function flash($name, $value = null)
    {
        if ($value !== null) {
            $_SESSION['_flash'][$name] = $value;
            return null;
        }
        if (!isset($_SESSION['_flash'][$name])) return '';
        $message = $_SESSION['_flash'][$name];
        unset($_SESSION['_flash'][$name]);
        return $message;
    }
}

if (!function_exists('getByUser')) {
    function getByUser($key) { return isset($_SESSION[$key]) ? $_SESSION[$key] : null; }
}

if (!function_exists('unsetUsers')) {
    function unsetUsers()
    {
        foreach (['name_temp', 'user_name_temp', 'email_temp', 'phon_temp', 'password_temp'] as $key) unset($_SESSION[$key]);
    }
}

if (!function_exists('limit_words')) {
    function limit_words($string, $wordLimit)
    {
        $words = preg_split('/\s+/u', trim((string) $string));
        return implode(' ', array_slice($words, 0, (int) $wordLimit));
    }
}

if (!function_exists('checkValue')) {
    function checkValue($array, $index) { return isset($array[$index]) ? $array[$index] : ''; }
}

if (!function_exists('dd')) {
    function dd($value) { echo '<pre>'; var_dump($value); exit; }
}

if (!function_exists('vd')) {
    function vd($value) { echo '<pre>'; var_dump($value); }
}

if (!function_exists('get_pag')) {
    function get_pag($currentPage, $path, $totalPages)
    {
        $currentPage = max(1, (int) $currentPage);
        $totalPages = max(1, (int) $totalPages);
        $from = max(1, $currentPage - 2);
        $to = min($totalPages, $currentPage + 2);
        $html = '';
        for ($i = $from; $i <= $to; $i++) {
            $url = htmlspecialchars(assets(trim($path, '/') . '/' . $i), ENT_QUOTES, 'UTF-8');
            $html .= "<li class='page-item'><a class='page-link' href='" . $url . "'>" . $i . "</a></li>";
        }
        return $html;
    }
}

if (!function_exists('get_pag_cat')) {
    function get_pag_cat($currentPage, $link, $where, $totalPages)
    {
        return get_pag($currentPage, trim($link, '/') . '/' . trim($where, '/'), $totalPages);
    }
}
