<?php

class ProxyHeadersFetcher extends ProxyFetcher {
    public function __construct(string $proxy, public string $headers){
        parent::__construct($proxy);
    }

    function fetch($url) : string {
        return self::fetchUrlWithHeaders($url, self::parseRawHeaders($this->headers), $this->proxy);
    }

}
