<?php
namespace Avetify\Files;

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
