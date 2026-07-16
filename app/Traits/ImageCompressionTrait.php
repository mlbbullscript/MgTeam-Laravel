<?php

namespace App\Traits;

use Carbon\Carbon;

trait ImageCompressionTrait
{
    /**
     * Kompres gambar, resize jika terlalu besar, dan ubah ke Base64.
     * Mengurangi ukuran file hingga 95%+, menghemat database, dan mencegah HP crash/lag saat rendering.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $maxWidth Maksimal lebar/tinggi gambar (default: 900px)
     * @param int $quality Kualitas kompresi JPEG (default: 65%)
     * @return string Data URL Base64
     */
    protected function compressAndEncodeToBase64($file, int $maxWidth = 900, int $quality = 65): string
    {
        $mime = $file->getMimeType();
        $realPath = $file->getRealPath();

        // Jika bukan gambar (misal PDF atau dokumen lain), langsung base64 tanpa kompresi
        if (!str_starts_with($mime, 'image/')) {
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($realPath));
        }

        try {
            // Periksa ekstensi GD library
            if (!extension_loaded('gd')) {
                return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($realPath));
            }

            // Muat gambar ke memori berdasarkan jenis mime
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = @imagecreatefromjpeg($realPath);
                    break;
                case 'image/png':
                    $image = @imagecreatefrompng($realPath);
                    break;
                case 'image/gif':
                    $image = @imagecreatefromgif($realPath);
                    break;
                case 'image/webp':
                    $image = @imagecreatefromwebp($realPath);
                    break;
                default:
                    $image = false;
            }

            // Jika gagal membuat gambar dari source, fallback ke file asli
            if (!$image) {
                return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($realPath));
            }

            // Dapatkan dimensi gambar asli
            $width = imagesx($image);
            $height = imagesy($image);

            // Lakukan resize jika lebar atau tinggi melebihi batas maxWidth
            if ($width > $maxWidth || $height > $maxWidth) {
                if ($width > $height) {
                    $newWidth = $maxWidth;
                    $newHeight = (int) ($height * ($maxWidth / $width));
                } else {
                    $newHeight = $maxWidth;
                    $newWidth = (int) ($width * ($maxWidth / $height));
                }

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

                // Salin dan perkecil dimensi gambar
                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resizedImage;
            }

            // Kompres gambar ke output buffer menggunakan format JPEG (ukuran terkecil)
            ob_start();
            imagejpeg($image, null, $quality);
            $compressedData = ob_get_clean();
            imagedestroy($image);

            return 'data:image/jpeg;base64,' . base64_encode($compressedData);
        } catch (\Throwable $e) {
            // Fallback aman jika terjadi kegagalan sistem
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($realPath));
        }
    }
}
