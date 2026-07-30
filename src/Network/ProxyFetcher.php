<?php
namespace Avetify\Network;

class ProxyFetcher extends NetworkFetcher {
    public function __construct(string $proxy){
        $this->proxy = $proxy;
    }

    function downloadFile($imageUrl, $targetFile) : bool {
        return $this->_downloadFile($imageUrl, $targetFile, $this->proxy);
    }
}
