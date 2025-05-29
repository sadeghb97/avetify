<?php
namespace Avetify\Utils;

/** @deprecated use Platform class in Interface package instead */
class CliUtils {
    public static function isCli() : bool {
        return php_sapi_name() == "cli";
    }
}
