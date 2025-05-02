<?php

class Routing {
    public static function getAvetifyRoot() : string {
        global $AVETIFY_ROOT_PATH;
        return $AVETIFY_ROOT_PATH;
    }

    public static function getAvetifyPhysicalRoot() : string {
        global $AVETIFY_PHYSICAL_ROOT_PATH;
        if(isCli()) return $AVETIFY_PHYSICAL_ROOT_PATH;
        return "";
    }

    public static function serverPathFromAvetify($path) : string {
        return self::getAvetifyPhysicalRoot() .
            $_SERVER['DOCUMENT_ROOT'] . self::getAvetifyRoot() . $path;
    }

    public static function serverRootPath($path) : string {
        return self::getAvetifyPhysicalRoot() .
            self::removeRedundantPath($_SERVER['DOCUMENT_ROOT'] . self::getAvetifyRoot() . '../') .
            $path;
    }

    public static function browserPathFromAvetify($path) : string {
        return self::getAvetifyRoot() . $path;
    }

    public static function getAvtImage($path) : string {
        return self::browserPathFromAvetify("assets/img/" . $path);
    }

    public static function getBackupFilesDir() : string {
        $dir = Routing::serverRootPath('.avtfiles/');
        if(!file_exists($dir)) mkdir($dir);
        return $dir;
    }

    public static function browserRootPath($path) : string {
        return self::removeRedundantPath(self::getAvetifyRoot() . '../') . $path;
    }

    public static function serverPathToBrowserPath($serverPath) : string {
        $adjustedPath = self::removeRedundantPath($serverPath);

        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        if(str_starts_with($adjustedPath, $documentRoot)){
            //a root address starts with /
            $adjustedPath = substr($adjustedPath, strlen($documentRoot));
        }

        return $adjustedPath;
    }

    public static function removeRedundantPath($path) : string {
        $startsWithSlash = str_starts_with($path, "/");
        $endsWithSlash = str_ends_with($path, "/");
        $parts = explode('/', $path);
        $stack = [];

        foreach ($parts as $part) {
            if ($part == '' || $part == '.') {
                continue;
            }
            elseif ($part == '..') {
                if (!empty($stack)) {
                    array_pop($stack);
                }
            }
            else {
                $stack[] = $part;
            }
        }

        // Rebuild the path
        return
            ($startsWithSlash ? "/" : "") . implode('/', $stack) . ($endsWithSlash ? "/" : "");
    }

    public static function prunePath($longPath, $needle) : string {
        if(str_contains($longPath, $needle)){
            $pos = strpos($longPath, $needle);
            return substr($longPath, $pos + strlen($needle));
        }
        return $longPath;
    }

    public static function getBaseUrlFilename(string $url) : string {
        if(str_contains($url, "/")){
            $pos = strrpos($url, "/");
            $url = substr($url, $pos + 1);
        }

        if(str_contains($url, "?")){
            $pos = strpos($url, "?");
            $url = substr($url, 0, $pos);
        }

        return $url;
    }

    public static function getServerProtocol() : string {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
            ? "https://" : "http://";
    }

    public static function currentPureLink() : string {
        $protocol = self::getServerProtocol();
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        $uriWithoutParams = strtok($uri, '?');
        return $protocol . $host . $uriWithoutParams;
    }

    public static function removeParamFromCurrentLink($paramKey) : string {
        $protocol = self::getServerProtocol();
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        $newRequestUri = self::removeQueryParamFromUrl($uri, $paramKey);
        return $protocol . $host . $newRequestUri;
    }

    public static function addParamToCurrentLink($paramKey, $paramValue="") : string {
        $protocol = self::getServerProtocol();
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        $newRequestUri = self::addParamToLink($uri, $paramKey, $paramValue);
        return $protocol . $host . $newRequestUri;
    }

    public static function removeQueryParamFromUrl(string $url, string $param) : string {
        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'] ?? '', $queryParams);

        if (isset($queryParams[$param])) {
            unset($queryParams[$param]);
        }

        $newQueryString = implode('&', array_map(
            fn($key, $value) => $value === '' ? $key : "{$key}=" . urlencode($value),
            array_keys($queryParams),
            $queryParams
        ));

        return
            (isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '') .
            ($parsedUrl['host'] ?? '') .
            ($parsedUrl['path'] ?? '') .
            (!empty($newQueryString) ? '?' . $newQueryString : '') .
            (isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '');
    }

    public static function addParamToLink($requestUri, $paramKey, $paramValue){
        $haveParam = !(!str_contains($requestUri, '?'));

        if(!$haveParam && $paramValue === null) return $requestUri;
        if(!$haveParam) return $requestUri . '?' . $paramKey .
            (strlen($paramValue) > 0 ? ('=' . $paramValue) : "");

        $qsPos = strpos($requestUri, '?');
        $leftRequestUri = substr($requestUri, 0, $qsPos);
        $rightRequestUri = strlen($requestUri) == ($qsPos + 1) ? "" : substr($requestUri, $qsPos + 1);

        $newRight = "";
        $pieces = explode('&', $rightRequestUri);

        $repetitive = false;
        foreach($pieces as $piece){
            $p = strpos($piece, '=');
            if($p === false || strlen($piece) == ($p + 1)){
                if($paramKey != $piece){
                    if($newRight) $newRight .= '&';
                    $newRight .= $piece;
                }
            }
            else {
                $k = substr($piece, 0, $p);
                $v = substr($piece, $p + 1);
                if($paramKey != $k){
                    if($newRight) $newRight .= '&';
                    $newRight .= $k;
                    $newRight .= '=';
                    $newRight .= $v;
                }
                else $repetitive = $v;
            }
        }

        if(strlen($newRight) > 0) $newRight = '?' . $newRight;
        if(!($paramValue === null)) {
            if ($newRight) $newRight .= "&";
            else $newRight .= '?';
            $newRight .= $paramKey;
            if($paramKey === "dir"){
                if($repetitive) {
                    if ($repetitive === "desc") $paramValue = "asc";
                    else $paramValue = "desc";
                }
            }

            if (strlen($paramValue) > 0) $newRight .= ('=' . $paramValue);
        }

        return $leftRequestUri . $newRight;
    }
}
