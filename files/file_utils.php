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

function convertImage($filename, $targetExtension, $maxImageSize = null,
                      $forcedWidthRatio = null, $forcedHeightRatio = null){
    if($forcedWidthRatio && $forcedHeightRatio){
        $format = 'mogrify -gravity center -crop %%[fx:min(w,h*%d/%d)]x%%[fx:min(h,w*%d/%d)]+0+0 +repage ';
        if($maxImageSize) $format .= "-resize %s ";
        $format .= "%s";

        $command = sprintf(
            $format,
            $forcedWidthRatio,         // Multiply height by the width ratio
            $forcedHeightRatio,        // Divide by height ratio
            $forcedHeightRatio,        // Multiply width by the height ratio
            $forcedWidthRatio,         // Divide by width ratio
            escapeshellarg($maxImageSize),   // Ensure max size is safe
            escapeshellarg($filename) // Secure the filename
        );

        echo $command . br();
        exec($command);
    }

    else {
        $command = "mogrify -format " . $targetExtension . " ";
        if ($maxImageSize) $command .= ("-resize " . $maxImageSize . " ");
        $command .= $filename;
        echo $command . br();
        exec($command);
    }
}
