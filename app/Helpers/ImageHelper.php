<?php
namespace App\Helpers;

use Intervention\Image\Laravel\Facades\Image;

class ImageHelper
{
    /**
     * Generate and save thumbnails for images.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $imageName
     * @param array $sizes
     * @param array $paths
     * @return void
     */
    public static function generateThumbnails($image, $imageName, array $sizes, array $paths)
    {
        $img = Image::read($image->path());

        foreach ($sizes as $index => $size) {
            $destinationPath = public_path($paths[$index]);
            [$width, $height] = $size;

            // Ensure destination path exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $resizedImage = clone $img; // Clone the image to avoid modifying the original instance

            $resizedImage->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . '/' . $imageName);
        }
    }
}
