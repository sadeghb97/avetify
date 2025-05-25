<?php
namespace Avetify\Utils;

class CliUtils {
    public static function isCli() : bool {
        return php_sapi_name() == "cli";
    }
}
