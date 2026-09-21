<?php

class Admin
{
    protected $currentDomain;
    protected $basePath;

    function __construct()
    {
        $Admin = new Auth();
        $Admin->checkAdmin();
        $this->currentDomain = CURRENT_DOMAIN;
        $this->basePath = BASE_PATH;
    }

    protected function redirect($url)
    {
        header('Location: ' . trim($this->currentDomain, '/ ') . '/' . trim($url, '/ '));
        exit;
    }

    protected function redirectBack($fallback = 'admin/dashboard')
    {
        $referer = isset($_SERVER['HTTP_REFERER']) ? trim($_SERVER['HTTP_REFERER']) : '';
        if ($referer !== '') {
            header('Location: ' . $referer);
            exit;
        }

        $this->redirect($fallback);
    }

    protected function saveImage($image, $imagePath, $imageName = null)
    {
        if (!is_array($image) || empty($image['tmp_name'])) {
            return false;
        }

        if (isset($image['error']) && (int) $image['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (!is_uploaded_file($image['tmp_name'])) {
            return false;
        }

        $extension = '';
        if (!empty($image['type']) && strpos($image['type'], '/') !== false) {
            $extension = strtolower(substr(strrchr($image['type'], '/'), 1));
        }

        $extensionMap = [
            'jpeg' => 'jpg',
            'pjpeg' => 'jpg',
            'x-png' => 'png',
        ];
        if (isset($extensionMap[$extension])) {
            $extension = $extensionMap[$extension];
        }

        if ($extension === '' && !empty($image['name'])) {
            $extension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        }
        if ($extension === '') {
            $extension = 'jpg';
        }

        $extension = preg_replace('/[^a-z0-9]+/i', '', $extension);
        if ($extension === '') {
            $extension = 'jpg';
        }

        if ($imageName === null) {
            $imageName = date('Y-m-d-H-i-s') . '-' . substr(md5(uniqid('', true)), 0, 8);
        }

        $relativePath = 'them/admin/dist/img/' . ltrim($imagePath, '/ ') . $imageName . '.' . $extension;
        $absolutePath = rtrim($this->basePath, '/\\') . '/' . ltrim($relativePath, '/');
        $directory = dirname($absolutePath);

        if (!is_dir($directory) && !@mkdir($directory, 0775, true)) {
            return false;
        }

        if (!move_uploaded_file($image['tmp_name'], $absolutePath)) {
            return false;
        }

        return $relativePath;
    }

    protected function removeImage($path)
    {
        if (empty($path)) {
            return;
        }

        $path = trim($this->basePath, '/ ') . '/' . trim($path, '/ ');
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
