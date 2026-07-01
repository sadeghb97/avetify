<?php
namespace Avetify\Network;

use Avetify\Utils\CliUtils;

class ManualNetFetcher extends NetworkFetcher {
    public function __construct(public bool $autoOpenUrl = false) {}

    function fetch($url) : string {
        if($this->autoOpenUrl){
            shell_exec('xdg-open ' . escapeshellarg($url) . ' > /dev/null 2>&1 &');
        }
        $res = CliUtils::readClipboardText("Enter Contents of (" . $url . ")");
        if($res === false) $this->lastStatusCode = 400;
        else $this->lastStatusCode = 200;
        return $res;
    }
}
