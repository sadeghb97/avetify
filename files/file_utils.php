<?php

function deleteDirectory($dir) : bool {
    if (!is_dir($dir)) {
        return false;
    }

    $files = array_diff(scandir($dir), ['.', '..']);

    foreach ($files as $file) {
        $filePath = "$dir/$file";
        if (is_dir($filePath)) {
            deleteDirectory($filePath);  // Recursively delete subdirectory
        } else {
            unlink($filePath);  // Delete file
        }
    }

    return rmdir($dir);  // Delete the directory itself
}

function getFileExtension($filename) : string {
    $pos = strrpos($filename, ".");
    if($pos < (strlen($filename) - 1)) return substr($filename, $pos + 1);
    return "";
}

function convertImage($filename, $targetExtension = null, $maxImageSize = null,
                      $forcedWidthRatio = null, $forcedHeightRatio = null){

    $commandStart = "mogrify " . ($targetExtension ? "-format " . $targetExtension . " " : "");
    $commandEnd = " " . $filename . ($targetExtension ? " && rm " . $filename : "");

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

    if(!$done && ($maxImageSize || $targetExtension)) {
        $command = $commandStart;
        if ($maxImageSize) $command .= ("-resize " . $maxImageSize . " ");
        $command .= $commandEnd;
        exec($command);
    }
}
