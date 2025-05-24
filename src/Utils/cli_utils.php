<?php
namespace Avetify\Utils;

function isCli() : bool {
    return php_sapi_name() == "cli";
}
