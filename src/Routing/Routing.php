<?php
namespace Avetify\Routing;

use Avetify\AvetifyManager;
use Avetify\Files\Filer;

class Routing {
    public static function serverRootPath($path) : string {
        return self::prunePath(AvetifyManager::publicPath($path));
    }

    public static function browserRootPath($path) : string {
        return self::prunePath(AvetifyManager::publicUrl($path));
    }

    public static function srpToBrp($serverPath) : string {
        return self::prunePath(AvetifyManager::convertPathToUrl($serverPath));
    }

    public static function brpToSrp($browserRootPath){
        return self::prunePath(AvetifyManager::convertUrlToPath($browserRootPath));
    }

    public static function getBackupFilesDir() : string {
        $dir = Routing::serverRootPath('.avtfiles/');
        if(!file_exists($dir)) mkdir($dir);
        return $dir;
    }

    public static function prunePath($path) : string {
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

    public static function cutPath($longPath, $needle) : string {
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
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') return 'https://';
        if (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 80) return 'http://';
        return '';
    }

    public static function currentPureLink() : string {
        $protocol = self::getServerProtocol();
        $host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "";
        $uri = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
        $uriWithoutParams = strtok($uri, '?');
        return $protocol . $host . $uriWithoutParams;
    }

    public static function currentScriptName() : string {
        $curLink = self::currentPureLink();
        $curLink = Filer::pruneLastSlash($curLink);
        if(!str_contains($curLink, "/")) return $curLink;
        $pos = strrpos($curLink, "/");
        if(strlen($curLink) <= ($pos + 1)) return "";
        return substr($curLink, $pos + 1);
    }

    public static function getUrlScheme($url) : string {
        $parts = parse_url($url);
        return !empty($parts['scheme']) ? $parts['scheme'] : "";
    }

    public static function getUrlHost($url) : string {
        $parts = parse_url($url);
        return !empty($parts['host']) ? $parts['host'] : "";
    }

    public static function getUrlPath($url) : string {
        $parts = parse_url($url);
        return !empty($parts['path']) ? $parts['path'] : "";
    }

    public static function getUrlQuery($url) : string {
        $parts = parse_url($url);
        return !empty($parts['query']) ? $parts['query'] : "";
    }

    public static function getUrlParams($url) : array {
        $parts = parse_url($url);
        if(empty($parts['query'])) return [];
        parse_str($parts['query'], $queryParams);
        return $queryParams;
    }

    public static function getUrlPureLink($url) : string {
        $parts = parse_url($url);
        $pureLink = "";
        if(!empty($parts['scheme'])){
            $pureLink .= ($parts['scheme'] . "//");
        }
        if(!empty($parts['path'])) $pureLink .= ($parts['path']);
        return $pureLink;
    }

    public static function removeParamFromCurrentLink($paramKey) : string {
        $protocol = self::getServerProtocol();
        $host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "";
        $uri = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
        $newRequestUri = self::removeQueryParamFromUrl($uri, $paramKey);
        return $protocol . $host . $newRequestUri;
    }

    public static function addParamToCurrentLink($paramKey, $paramValue="") : string {
        $protocol = self::getServerProtocol();
        $host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "";
        $uri = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
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
