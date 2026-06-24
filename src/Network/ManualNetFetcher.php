<?php
namespace Avetify\Network;

use Avetify\Utils\CliUtils;

class ManualNetFetcher extends NetworkFetcher {
    function fetch($url) : string {
        $res = CliUtils::readClipboardText("Enter Contents of (" . $url . ")");
        if($res === false) $this->lastStatusCode = 400;
        else $this->lastStatusCode = 200;
        return $res;
    }
}
