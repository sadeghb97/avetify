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

    public static function transliterateToAscii(string $text): string {
        static $transliterator = null;

        if ($transliterator === null) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
        }

        $text = rawurldecode($text);
        return $transliterator->transliterate($text);
    }

    public static function generateUUID(): string {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(bin2hex($bytes), 4)
        );
    }
}
