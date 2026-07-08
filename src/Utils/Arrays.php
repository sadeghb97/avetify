<?php
namespace Avetify\Utils;

class Arrays {
    public static function getRandomSubarray(array $array, int $length): array {
        shuffle($array);
        return array_slice($array, 0, $length);
    }
}
