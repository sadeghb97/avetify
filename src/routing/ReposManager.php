<?php

class ReposManager {
    private static function serverPathFromAvetify($path) : string {
        return Routing::getPHPDocumentRoot() . Routing::getAvetifyRoot() . $path;
    }

    public static function getFile(string $avtRelativePath) : string {
        return self::serverPathFromAvetify("$avtRelativePath");
    }

    public static function getRepo(string $avtRelativePath) : string {
        return self::getFile("data/$avtRelativePath");
    }
}
