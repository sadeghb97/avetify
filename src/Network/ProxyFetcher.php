<?php
namespace Avetify\Network;

class ProxyFetcher extends NetworkFetcher {
    public function __construct(public string $proxy){}

    function enableProxy(){
        stream_context_set_default(['http'=>['proxy' => $this->proxy]]);
    }

    function fetch($url) : string {
        return $this->curlGetContents($url, $this->proxy);
    }

    function downloadFile($imageUrl, $targetFile) : bool {
        return $this->_downloadFile($imageUrl, $targetFile, $this->proxy);
    }
}
