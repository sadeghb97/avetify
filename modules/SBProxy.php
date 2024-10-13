<?php

class SBProxy {
    public function __construct(public string $proxy){}

    function enableProxy(){
        stream_context_set_default(['http'=>['proxy' => $this->proxy]]);
    }

    function getContents($url) : string {
        return curlGetContents($url, $this->proxy);
    }

    function downloadFile($imageUrl, $targetFile) : bool{
        return downloadFile($imageUrl, $targetFile, $this->proxy);
    }
}
