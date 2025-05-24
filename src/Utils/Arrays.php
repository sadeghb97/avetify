<?php
namespace Avetify\Utils;

class Arrays {
    public static function getRandomSubarray(array $array, int $length): array {
        if ($length >= count($array)) {
            return $array;
        }

        $keys = array_rand($array, $length);
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        return array_intersect_key($array, array_flip($keys));
    }
}
