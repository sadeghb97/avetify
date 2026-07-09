<?php
namespace Avetify\Searchers\Utils;

use Avetify\Entities\AvtEntityItem;

class EntitySearcherUtils {
    public static function safeStr(mixed $v): string {
        if($v === null) return "";
        return (string)$v;
    }

    public static function listToTrimmedAliases(string $aliases): array {
        $parts = explode(",", $aliases);
        $out = [];
        foreach ($parts as $p){
            $p = trim($p);
            if($p !== "") $out[] = $p;
        }
        return $out;
    }

    public static function normalizeText(string $s): string {
        if($s === "") return "";
        $s = mb_strtolower($s, "UTF-8");
        if(function_exists("normalizer_normalize")){
            $n = normalizer_normalize($s, \Normalizer::FORM_D);
            if($n !== false){
                $s = preg_replace('/\p{Mn}/u', "", $n) ?? $s;
            }
        }
        return trim($s);
    }

    /** @param string[] $tokens */
    public static function buildSearchIndex(array $tokens): string {
        $out = [];
        $seen = [];
        foreach ($tokens as $token){
            $token = self::safeStr($token);
            if($token === "") continue;
            $n = self::normalizeText($token);
            if($n === "" || isset($seen[$n])) continue;
            $seen[$n] = true;
            $out[] = $n;
        }
        return implode(" | ", $out);
    }

    /**
     * @param AvtEntityItem[] $items
     * @return array<int, AvtEntityItem>
     */
    public static function itemsById(array $items): array {
        $out = [];
        foreach ($items as $item){
            $out[(int)$item->getItemId()] = $item;
        }
        return $out;
    }
}
