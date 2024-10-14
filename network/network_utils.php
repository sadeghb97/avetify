<?php

function curlGetContents($url, $proxy = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3");
    curl_setopt($ch, CURLOPT_ENCODING, "");

    if($proxy){
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
    }

    $fileContent = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo $httpCode . br();
    if ($httpCode == 200 && $fileContent !== false) {
        return $fileContent;
    }

    return false;
}

function downloadFile($fileUrl, $targetFile, $proxy = null) : bool {
    $fileContent = curlGetContents($fileUrl, $proxy);
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
        if (strpos($line, ':') === false) continue;
        $headersArray[] = $line;
    }

    return $headersArray;
}