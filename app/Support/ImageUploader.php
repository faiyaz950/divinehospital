<?php

namespace App\Support;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Saves admin uploads into public/images/uploads so they work on shared
 * hosting without a storage symlink.
 */
class ImageUploader
{
    /** Widths generated for responsive <x-picture> images. */
    public const PICTURE_WIDTHS = [640, 1024, 1600];

    private const DIRECTORY = 'images/uploads';

    /**
     * Store a photo as responsive WebP files ({name}-640.webp, -1024, -1600).
     *
     * @return string Picture name for <x-picture>, relative to public/images (e.g. "uploads/lobby-a1b2c3").
     */
    public function storePicture(UploadedFile $file): string
    {
        $source = $this->load($file);
        $name = $this->baseName($file);
        $originalWidth = imagesx($source);

        foreach (self::PICTURE_WIDTHS as $index => $width) {
            if ($index > 0 && $width > $originalWidth) {
                break;
            }

            $this->saveWebp($this->resize($source, min($width, $originalWidth)), public_path(self::DIRECTORY."/{$name}-{$width}.webp"));
        }

        return 'uploads/'.$name;
    }

    /**
     * Store a single image (logo, portrait, award…). Converted to WebP unless the original format must be kept.
     *
     * @return string Path relative to public/ (e.g. "images/uploads/doctor-a1b2c3.webp").
     */
    public function storeImage(UploadedFile $file, bool $keepFormat = false, int $maxWidth = 1200): string
    {
        $name = $this->baseName($file);
        $this->ensureDirectory();

        if ($keepFormat || $file->getMimeType() === 'image/gif') {
            $path = self::DIRECTORY."/{$name}.".$file->guessExtension();
            $file->move(public_path(self::DIRECTORY), basename($path));

            return $path;
        }

        $source = $this->load($file);
        $path = self::DIRECTORY."/{$name}.webp";
        $this->saveWebp($this->resize($source, min($maxWidth, imagesx($source))), public_path($path));

        return $path;
    }

    private function load(UploadedFile $file): GdImage
    {
        $image = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if (! $image instanceof GdImage) {
            throw new RuntimeException('The uploaded file is not a readable image.');
        }

        return $this->applyExifOrientation($image, $file);
    }

    /** Phone photos are often stored sideways with an EXIF rotation flag. */
    private function applyExifOrientation(GdImage $image, UploadedFile $file): GdImage
    {
        if (! function_exists('exif_read_data') || $file->getMimeType() !== 'image/jpeg') {
            return $image;
        }

        $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;

        return match ((int) $orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };
    }

    private function resize(GdImage $source, int $width): GdImage
    {
        $height = (int) round(imagesy($source) * $width / imagesx($source));
        $target = imagecreatetruecolor($width, $height);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));

        return $target;
    }

    private function saveWebp(GdImage $image, string $path): void
    {
        $this->ensureDirectory();
        imagewebp($image, $path, 82);
    }

    private function baseName(UploadedFile $file): string
    {
        $slug = Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))->slug()->limit(40, '')->toString();

        return ($slug ?: 'image').'-'.Str::lower(Str::random(6));
    }

    private function ensureDirectory(): void
    {
        File::ensureDirectoryExists(public_path(self::DIRECTORY));
    }
}
