<?php
// Keep central DB configuration dependency for legacy compatibility.
$dbConfig = require __DIR__ . '/config/database.php';
unset($dbConfig);

if (!defined('BASE_PATH')) {
    $slug = isset($_GET['city']) ? preg_replace('/[^a-zA-Z0-9-]/', '', (string)$_GET['city']) : '';
    header('Location: /services-in-' . ($slug !== '' ? $slug : 'tehran'), true, 301);
    exit;
}
require BASE_PATH . '/them/app/cities/show.php';
