<?php
if (!defined('BASE_PATH')) {
    $slug = isset($_GET['city']) ? preg_replace('/[^a-zA-Z0-9-]/', '', (string)$_GET['city']) : '';
    header('Location: /services-in-' . ($slug !== '' ? $slug : 'tehran'), true, 301);
    exit;
}
require BASE_PATH . '/them/app/cities/show.php';
