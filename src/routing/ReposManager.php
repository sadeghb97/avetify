<?php

class ReposManager {
    private static function serverPathFromAvetify($path) : string {
        return Routing::getPHPDocumentRoot() . Routing::getAvetifyRoot() . $path;
    }

    public static function getRepo(string $avtRelativePath) : string {
        return self::serverPathFromAvetify("data/$avtRelativePath");
    }
}
