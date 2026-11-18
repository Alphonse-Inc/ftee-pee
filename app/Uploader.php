<?php
// Image uploads: type, size, pixel cap, random name. Resize with GD when it exists.

class Uploader
{
    const MAX_BYTES = 3145728;
    const MAX_EDGE = 1800;
    const MAX_PIXELS = 20000000;

    public static function store(array $file, $folder)
    {
        if (!isset($file['error']) || (int) $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['ok' => false, 'error' => 'Choose an image to upload.'];
        }
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'The upload did not finish. Try a smaller image.'];
        }
        $tmp = isset($file['tmp_name']) ? (string) $file['tmp_name'] : '';
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return ['ok' => false, 'error' => 'The upload was rejected.'];
        }
        $size = isset($file['size']) ? (int) $file['size'] : 0;
        if ($size < 1 || $size > self::MAX_BYTES) {
            return ['ok' => false, 'error' => 'Images must be 3 MB or smaller.'];
        }
        $mime = self::mime($tmp);
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($map[$mime])) {
            return ['ok' => false, 'error' => 'Upload a JPG, PNG, or WebP image.'];
        }
        $info = @getimagesize($tmp);
        if (!is_array($info) || empty($info[0]) || empty($info[1])) {
            return ['ok' => false, 'error' => 'That file is not a readable image.'];
        }
        $width = (int) $info[0];
        $height = (int) $info[1];
        if ($width < 1 || $height < 1 || ($width * $height) > self::MAX_PIXELS) {
            return ['ok' => false, 'error' => 'That image is too large in pixels.'];
        }
        $folder = trim((string) $folder, '/');
        if (!preg_match('/^[a-z0-9_-]+$/', $folder)) {
            return ['ok' => false, 'error' => 'The upload folder is not valid.'];
        }
        $dir = app_root() . '/uploads/' . $folder;
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            return ['ok' => false, 'error' => 'The uploads folder is not writable. Set permissions to 755 or 775.'];
        }
        $name = bin2hex(random_bytes(16)) . '.' . $map[$mime];
        $dest = $dir . '/' . $name;
        if (!@move_uploaded_file($tmp, $dest)) {
            return ['ok' => false, 'error' => 'The image could not be saved.'];
        }
        @chmod($dest, 0644);
        $dims = self::maybeResize($dest, $mime, $width, $height);
        return [
            'ok' => true,
            'path' => $folder . '/' . $name,
            'width' => $dims[0],
            'height' => $dims[1],
        ];
    }

    public static function delete($path)
    {
        $path = (string) $path;
        if ($path === '' || strpos($path, 'assets/') === 0 || strpos($path, '..') !== false) {
            return;
        }
        $full = app_root() . '/uploads/' . ltrim($path, '/');
        if (is_file($full)) {
            @unlink($full);
        }
    }

    private static function mime($tmp)
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = finfo_file($finfo, $tmp);
                finfo_close($finfo);
                if (is_string($mime) && $mime !== '') {
                    return $mime;
                }
            }
        }
        $info = @getimagesize($tmp);
        if (is_array($info) && !empty($info['mime'])) {
            return (string) $info['mime'];
        }
        return '';
    }

    private static function maybeResize($path, $mime, $width, $height)
    {
        if (!function_exists('imagecreatetruecolor')) {
            return [$width, $height];
        }
        $edge = max($width, $height);
        if ($edge <= self::MAX_EDGE) {
            return [$width, $height];
        }
        $scale = self::MAX_EDGE / $edge;
        $newW = max(1, (int) round($width * $scale));
        $newH = max(1, (int) round($height * $scale));
        $src = self::open($path, $mime);
        if (!$src) {
            return [$width, $height];
        }
        $dst = imagecreatetruecolor($newW, $newH);
        if ($dst === false) {
            imagedestroy($src);
            return [$width, $height];
        }
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $clear = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $newW, $newH, $clear);
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
        $saved = self::save($dst, $path, $mime);
        imagedestroy($src);
        imagedestroy($dst);
        if (!$saved) {
            return [$width, $height];
        }
        return [$newW, $newH];
    }

    private static function open($path, $mime)
    {
        if ($mime === 'image/jpeg' && function_exists('imagecreatefromjpeg')) {
            return @imagecreatefromjpeg($path);
        }
        if ($mime === 'image/png' && function_exists('imagecreatefrompng')) {
            return @imagecreatefrompng($path);
        }
        if ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
            return @imagecreatefromwebp($path);
        }
        return false;
    }

    private static function save($image, $path, $mime)
    {
        if ($mime === 'image/jpeg' && function_exists('imagejpeg')) {
            return imagejpeg($image, $path, 82);
        }
        if ($mime === 'image/png' && function_exists('imagepng')) {
            return imagepng($image, $path, 6);
        }
        if ($mime === 'image/webp' && function_exists('imagewebp')) {
            return imagewebp($image, $path, 82);
        }
        return false;
    }
}
