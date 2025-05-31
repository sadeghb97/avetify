<?php
namespace Avetify;

class AvetifyManager {
    private static string $basePath;
    private static string $publicBasePath;
    private static string $publicBaseUrl;
    private static string $assetBaseUrl;
    private static string $assetBasePath;
    private static string $dataBasePath;

    public const AVETIFY_VERSION = "0.1.0";
    public const AVETIFY_BUILD_NUMBER = 1;

    public static function init(string $basePath, string $publicPath, string $publicUrl, string $assetUrl): void {
        self::$basePath   = rtrim($basePath, DIRECTORY_SEPARATOR);
        self::$publicBasePath = rtrim($publicPath, DIRECTORY_SEPARATOR);
        self::$publicBaseUrl  = rtrim($publicUrl, '/');
        self::$assetBaseUrl   = rtrim($assetUrl, '/');
        self::$assetBasePath  = __DIR__ . '/../assets';
        self::$dataBasePath   = __DIR__ . '/../data';
    }

    /**
     * Returns the full URL to an asset file.
     */
    public static function assetUrl(string $path = ''): string {
        return self::$assetBaseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Returns the full URL to an image file.
     */
    public static function imageUrl(string $path = ''): string {
        return self::assetUrl("img/$path");
    }

    /**
     * Returns the full physical filesystem path to an asset file.
     */
    public static function assetPath(string $path = ''): string {
        return self::$assetBasePath . '/' . ltrim($path, '/');
    }

    /**
     * Returns the full physical filesystem path to a data file.
     */
    public static function dataPath(string $path = ''): string {
        return self::$dataBasePath . '/' . ltrim($path, '/');
    }

    /**
     * Returns the base directory path of the project (the root path).
     */
    public static function basePath(): string {
        return self::$basePath;
    }

    /**
     * Returns the public directory path (web-accessible root).
     */
    public static function publicBasePath(): string {
        return self::$publicBasePath;
    }

    /**
     * Returns the full physical filesystem path to a file or directory in the project root.
     */
    public static function publicPath(string $path = ''): string {
        return self::$publicBasePath . '/' . ltrim($path, '/');
    }

    /**
     * Returns the full URL to a file or resource in the public directory.
     */
    public static function publicUrl(string $path = ''): string {
        return self::$publicBaseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Converts an absolute physical path (inside the public directory) to a relative URL.
     *
     * @param string $absolutePath Full path to a file in the public directory
     * @return string|null URL path relative to the web root, or null if not inside public dir
     */
    public static function convertPathToUrl(string $absolutePath): ?string {
        $absolutePath = !str_contains($absolutePath, "?") ? realpath($absolutePath) : $absolutePath;
        $publicPath   = realpath(self::$publicBasePath);

        if ($absolutePath && str_starts_with($absolutePath, $publicPath)) {
            $relative = ltrim(substr($absolutePath, strlen($publicPath)), DIRECTORY_SEPARATOR);
            return self::publicUrl($relative);
        }

        return null;
    }

    public static function convertUrlToPath(string $url): string {
        // Remove the base public URL if present
        if (str_starts_with($url, self::$publicBaseUrl)) {
            $url = substr($url, strlen(self::$publicBaseUrl));
        }

        $url = '/' . ltrim($url, '/');

        return self::$publicBasePath . $url;
    }
}
