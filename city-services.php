<?php
// Keep central DB configuration dependency for legacy compatibility.
$dbConfig = require __DIR__ . '/config/database.php';
unset($dbConfig);
$city = isset($_GET['city']) ? preg_replace('/[^a-zA-Z0-9-]/', '', (string)$_GET['city']) : 'tehran';
header('Location: /services-in-' . $city, true, 301);
exit;
