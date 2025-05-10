<?php

use JetBrains\PhpStorm\Pure;

class APIHelper {
    public static function getRequestJSONParams(){
        $paramsRaw = file_get_contents("php://input");
        return json_decode($paramsRaw, true);
    }

    public static function getPostParam(array $jsonParams, string $paramKey) : ?string {
        if(isset($_POST[$paramKey])) return $_POST[$paramKey];
        if(isset($jsonParams[$paramKey])) return $jsonParams[$paramKey];
        return null;
    }

    #[Pure] public static function reqTypeMatch(string $targetType, array $jsonParams) : bool {
        $type = self::getPostParam($jsonParams, "request_type");
        return $targetType == $type;
    }
}
