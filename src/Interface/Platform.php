<?php
namespace Avetify\Interface;

class Platform {
    public static function isCli() : bool {
        return php_sapi_name() == "cli";
    }
}
