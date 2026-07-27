<?php
namespace Avetify\Utils;

class StringUtils {
    public static function slugify(string $str) : string {
        return strtolower(str_replace(" ", "_", $str));
    }

    public static function titlify(string $str) : string {
        return ucwords(str_replace("_", " ", $str));
    }

    public static function minimize(string $str, int $maxSize) : string {
        if($maxSize < 1) return $str;
        $strLength = strlen($str);
        if($strLength < ($maxSize + 3)) return $str;

        $halfStart = intdiv($maxSize, 2);
        $halfEnd = $halfStart;
        if(($maxSize % 2) != 0) $halfStart++;

        $startStr = substr($str, 0, $halfStart);
        $endStr = substr($str, $strLength - $halfEnd);

        return $startStr . "..." . $endStr;
    }

    public static function normalizeSearch(string $text): string {
        static $transliterator = null;

        if ($transliterator === null) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
        }

        return $transliterator->transliterate($text);
    }
}
