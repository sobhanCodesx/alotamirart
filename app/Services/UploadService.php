<?php
namespace App\Services;

class UploadService
{
    private $basePath;
    private $mimeMap = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    public function __construct($basePath) { $this->basePath = rtrim($basePath, '/\\'); }

    public function image(array $file, $relativeDirectory, $prefix = 'img-')
    {
        if (empty($file['tmp_name']) || !isset($file['error']) || (int) $file['error'] !== UPLOAD_ERR_OK) return null;
        if (!is_uploaded_file($file['tmp_name'])) return null;

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!isset($this->mimeMap[$mime])) return null;

        $directory = trim($relativeDirectory, '/\\');
        $absoluteDirectory = $this->basePath . '/' . $directory;
        if (!is_dir($absoluteDirectory) && !@mkdir($absoluteDirectory, 0775, true)) return null;

        $name = $prefix . bin2hex(random_bytes(8)) . '.' . $this->mimeMap[$mime];
        $absolute = $absoluteDirectory . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $absolute)) return null;

        return $directory . '/' . $name;
    }

    public function remove($relativePath)
    {
        if (!$relativePath) return;
        $relativePath = ltrim(str_replace(['..', '\\'], ['', '/'], $relativePath), '/');
        $absolute = $this->basePath . '/' . $relativePath;
        if (is_file($absolute)) @unlink($absolute);
    }
}
