<?php
namespace Avetify\Network;

class NetworkFetcher {
    public int $lastStatusCode = 0;
    public int $redirectCount = 0;
    public string $finalUrl = "";
    public ?string $proxy = null;
    public int $connectTimeout = 0;
    public int $wholeTimeout = 0;

    public function fetch($url) : string {
        return $this->curlGetContents($url, $this->proxy);
    }

    protected function curlGetContents($url, $proxy = null) : string | bool {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3");
        curl_setopt($ch, CURLOPT_ENCODING, "");

        if($proxy) curl_setopt($ch, CURLOPT_PROXY, $proxy);
        if($this->connectTimeout > 0) curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        if($this->wholeTimeout > 0) curl_setopt($ch, CURLOPT_TIMEOUT, $this->wholeTimeout);

        $fileContent = curl_exec($ch);
        $this->lastStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->redirectCount = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
        $this->finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        if ($this->lastStatusCode >= 200 && $this->lastStatusCode < 300 && $fileContent !== false) {
            return $fileContent;
        }

        return false;
    }

    public function downloadFile($fileUrl, $targetFile) : bool {
        $fileContent = $this->curlGetContents($fileUrl, $this->proxy);
        if ($fileContent) {
            file_put_contents($targetFile, $fileContent);
            return true;
        }
        return false;
    }

    function fetchUrlWithHeaders($url, $headers, $proxy = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");

        if($proxy){
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        $response = curl_exec($ch);
        $this->lastStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return $response;
    }

    function parseRawHeaders($rawHeaders) : array {
        $lines = explode("\n", $rawHeaders);
        $headersArray = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            if (!str_contains($line, ':')) continue;
            $headersArray[] = $line;
        }

        return $headersArray;
    }

    public function setProxy(string $proxy) : static {
        $this->proxy = $proxy;
        return $this;
    }

    public function setConnectTimeout(int $timeout) : static {
        $this->connectTimeout = $timeout;
        return $this;
    }

    public function setWholeTimeout(int $timeout) : static {
        $this->wholeTimeout = $timeout;
        return $this;
    }
}
