<?php
/**
 * Convert an uploaded image to WebP format.
 *
 * @param string $source Path file sementara (tmp_name).
 * @param string $destination Path tujuan untuk menyimpan file .webp.
 * @param int $quality Kualitas gambar WebP (0-100).
 * @return bool True jika berhasil, false jika gagal.
 */
function convertToWebp($source, $destination, $quality = 80) {
    $info = @getimagesize($source);
    if ($info === false) {
        return false;
    }

    $mime = $info['mime'];
    $image = null;

    switch ($mime) {
        case 'image/jpeg':
            $image = @imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($source);
            if ($image !== false) {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }
            break;
        case 'image/webp':
            return move_uploaded_file($source, $destination);
        default:
            return false;
    }

    if ($image === false) {
        return false;
    }

    $success = imagewebp($image, $destination, $quality);
    imagedestroy($image);
    
    return $success;
}
?>
