<?php

namespace App\Http\Services;

/**
 * Crops the blank margin around a product photo so the item fills more of the catalog
 * card. Product photography is usually shot with generous padding around the item —
 * without this, a bigger image box on the card is mostly empty space, not a bigger
 * product. Results are cached to disk by source path + mtime since the same photo is
 * reused across many catalogs.
 */
class ProductImageTrimmer
{
    private const CACHE_DIR = 'app/product_catalogs/trimmed_images';

    /** Euclidean RGB distance (0-441) under which a pixel counts as background. */
    private const COLOR_TOLERANCE = 18;

    /** Skip trimming when it would not free up at least this fraction of the canvas. */
    private const MIN_REDUCTION = 0.04;

    /**
     * Returns the absolute path to a trimmed copy of $absolutePath, or null when the
     * source could not be read or trimming would not meaningfully change it (the
     * caller should keep using the original image in that case).
     */
    public function trim(string $absolutePath): ?string
    {
        if (! is_file($absolutePath)) {
            return null;
        }

        $cachePath = $this->cachePath($absolutePath);

        if (is_file($cachePath)) {
            return $cachePath;
        }

        $cropped = $this->crop($absolutePath);

        if (! $cropped) {
            return null;
        }

        $directory = dirname($cachePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        imagepng($cropped, $cachePath);
        imagedestroy($cropped);

        return $cachePath;
    }

    protected function cachePath(string $absolutePath): string
    {
        $key = md5($absolutePath . '|' . filemtime($absolutePath));

        return storage_path(self::CACHE_DIR . '/' . $key . '.png');
    }

    /**
     * @return \GdImage|null
     */
    protected function crop(string $path)
    {
        $info = @getimagesize($path);

        if (! $info) {
            return null;
        }

        $source = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            default => null,
        };

        if (! $source) {
            return null;
        }

        imagealphablending($source, false);
        imagesavealpha($source, true);

        $width = imagesx($source);
        $height = imagesy($source);
        $hasAlpha = in_array($info['mime'], ['image/png', 'image/webp'], true);

        // The corner pixel stands in for the background colour: catalog photos are shot
        // on a flat backdrop (usually white), so a single sample is enough.
        $background = imagecolorsforindex($source, imagecolorat($source, 0, 0));

        $box = $this->boundingBox($source, $width, $height, $background, $hasAlpha);

        if (! $box) {
            imagedestroy($source);
            return null;
        }

        [$minX, $minY, $maxX, $maxY] = $box;
        $cropWidth = $maxX - $minX + 1;
        $cropHeight = $maxY - $minY + 1;

        if ($cropWidth >= $width * (1 - self::MIN_REDUCTION) && $cropHeight >= $height * (1 - self::MIN_REDUCTION)) {
            imagedestroy($source);
            return null;
        }

        $destination = imagecreatetruecolor($cropWidth, $cropHeight);
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 0, 0, 0, 127);
        imagefilledrectangle($destination, 0, 0, $cropWidth, $cropHeight, $transparent);
        imagealphablending($destination, true);

        imagecopy($destination, $source, 0, 0, $minX, $minY, $cropWidth, $cropHeight);
        imagedestroy($source);

        return $destination;
    }

    /**
     * Scans the image (downsampled for speed on large photos) for the bounding box of
     * every pixel that is not background, plus a small padding margin.
     *
     * @return array{0:int,1:int,2:int,3:int}|null
     */
    protected function boundingBox($image, int $width, int $height, array $background, bool $hasAlpha): ?array
    {
        $stepX = max(1, (int) ($width / 500));
        $stepY = max(1, (int) ($height / 500));

        $minX = $width;
        $minY = $height;
        $maxX = 0;
        $maxY = 0;
        $found = false;

        for ($y = 0; $y < $height; $y += $stepY) {
            for ($x = 0; $x < $width; $x += $stepX) {
                $pixel = imagecolorsforindex($image, imagecolorat($image, $x, $y));

                if ($this->isBackground($pixel, $background, $hasAlpha)) {
                    continue;
                }

                $found = true;
                $minX = min($minX, $x);
                $maxX = max($maxX, $x);
                $minY = min($minY, $y);
                $maxY = max($maxY, $y);
            }
        }

        if (! $found) {
            return null;
        }

        $paddingX = (int) round($width * 0.015);
        $paddingY = (int) round($height * 0.015);

        return [
            max(0, $minX - $paddingX),
            max(0, $minY - $paddingY),
            min($width - 1, $maxX + $paddingX),
            min($height - 1, $maxY + $paddingY),
        ];
    }

    protected function isBackground(array $pixel, array $background, bool $hasAlpha): bool
    {
        if ($hasAlpha && $pixel['alpha'] >= 100) {
            return true;
        }

        $distance = sqrt(
            (($pixel['red'] - $background['red']) ** 2)
            + (($pixel['green'] - $background['green']) ** 2)
            + (($pixel['blue'] - $background['blue']) ** 2)
        );

        return $distance <= self::COLOR_TOLERANCE;
    }
}
