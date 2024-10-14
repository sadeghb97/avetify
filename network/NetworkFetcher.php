<?php
class NetworkFetcher {
    function downloadFile($imageUrl, $targetFile) : bool{
        return downloadFile($imageUrl, $targetFile, null);
    }

    public function fetch($url) : string {
        return curlGetContents($url);
    }
}
