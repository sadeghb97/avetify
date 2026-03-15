<?php
namespace Avetify\Files;

use Exception;

class ImageUtils {
    public static function convert($filename, $targetExtension = null, $maxImageSize = null,
                                   $forcedWidthRatio = null, $forcedHeightRatio = null){
        $orgFileExtension = Filer::getFileExtension($filename);
        $commandStart = "env -i /usr/bin/mogrify " . ($targetExtension ? "-format " . $targetExtension . " " : "");
        $commandEnd = " " . $filename . " 2>&1";

        $done = false;
        if($forcedWidthRatio && $forcedHeightRatio){
            $targetRatio = $forcedWidthRatio / $forcedHeightRatio;
            $imageSize = getimagesize($filename);
            $imageWidth = $imageSize[0];
            $imageHeight = $imageSize[1];
            $imageRatio = $imageWidth / $imageHeight;

            $diff = abs($imageRatio - $targetRatio);
            if($diff > 0.01) {
                $cropSize = $imageRatio > $targetRatio ?
                    (((int)($imageHeight * $targetRatio)) . "x" . $imageHeight) :
                    ($imageWidth . "x" . ((int)($imageWidth / $targetRatio)));

                $command = $commandStart;
                $command .= "-gravity center -crop $cropSize+0+0 +repage ";
                if ($maxImageSize) $command .= ("-resize " . $maxImageSize . " ");
                $command .= $commandEnd;
                exec($command);
                $done = true;
            }
        }

        // max image size = max image width in pixels
        if(!$done && ($maxImageSize || $targetExtension)) {
            $command = $commandStart;
            if ($maxImageSize) $command .= ("-resize " . $maxImageSize . " ");
            $command .= $commandEnd;
            exec($command);

            if($targetExtension && $targetExtension != $orgFileExtension){
                exec("rm " . $filename);
            }
        }
    }

    public static function rotateImage90($inputPath, $outputPath = null) : void {
        if (!file_exists($inputPath)) {
            throw new Exception("File not found: $inputPath");
        }

        $info = getimagesize($inputPath);
        if ($info === false) {
            throw new Exception("Invalid image file: $inputPath");
        }

        $mime = $info['mime'];

        // Read the image depending on its MIME type
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($inputPath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($inputPath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($inputPath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($inputPath);
                break;
            case 'image/bmp':
            case 'image/x-ms-bmp':
                $image = imagecreatefrombmp($inputPath);
                break;
            default:
                throw new Exception("Unsupported image format: $mime");
        }

        $rotated = imagerotate($image, -90, 0);

        if ($outputPath === null) {
            $pathInfo = pathinfo($inputPath);
            $outputPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_rotated.' . $pathInfo['extension'];
        }

        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($rotated, $outputPath, 100);
                break;
            case 'image/png':
                imagepng($rotated, $outputPath);
                break;
            case 'image/gif':
                imagegif($rotated, $outputPath);
                break;
            case 'image/webp':
                imagewebp($rotated, $outputPath);
                break;
            case 'image/bmp':
            case 'image/x-ms-bmp':
                imagebmp($rotated, $outputPath);
                break;
        }

        imagedestroy($image);
        imagedestroy($rotated);

        echo "✅ Rotated image saved at: " . $outputPath . PHP_EOL;
    }

    public static function getRatio(string $imageFilename) : float {
        $imageSize = getimagesize($imageFilename);
        $imageWidth = $imageSize[0];
        $imageHeight = $imageSize[1];
        return $imageWidth / $imageHeight;
    }

    public static function getMaxDimSize(string $imageFilename) : float {
        $imageSize = getimagesize($imageFilename);
        return max($imageSize[0], $imageSize[1]);
    }

    public static function getRatioDiff(string $imageFilename, float $targetRatio) : float {
        $imageRatio = self::getRatio($imageFilename);
        return abs($imageRatio - $targetRatio);
    }

    public static function getRatioDiffWithDims(string $imageFilename, float $widthDim, float $heightDim) : float {
        return self::getRatioDiff($imageFilename, $widthDim / $heightDim);
    }

    public static function getImageExtension(int $imageType, bool $includeDot = false) : string {
        $ext = image_type_to_extension($imageType, $includeDot);
        if($ext == "jpeg") return "jpg";
        return $ext;
    }
}
