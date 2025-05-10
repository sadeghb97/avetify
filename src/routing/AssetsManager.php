<?php

class AssetsManager {
    private static function browserPathFromAvetify($path) : string {
        return Routing::getAvetifyRoot() . $path;
    }

    public static function getAsset(string $avtRelativePath) : string {
        return self::browserPathFromAvetify("assets/$avtRelativePath");
    }

    public static function getImage(string $avtRelativePath) : string {
        return self::getAsset("img/$avtRelativePath");
    }
}
