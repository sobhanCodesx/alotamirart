<?php
$city = isset($_GET['city']) ? preg_replace('/[^a-zA-Z0-9-]/', '', (string)$_GET['city']) : 'tehran';
header('Location: /services-in-' . $city, true, 301);
exit;
