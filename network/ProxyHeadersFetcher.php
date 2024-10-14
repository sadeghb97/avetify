<?php

class ProxyHeadersFetcher extends ProxyFetcher {
    public function __construct(string $proxy, public string $headers){
        parent::__construct($proxy);
    }

    function fetch($url) : string {
        return fetchUrlWithHeaders($url, parseRawHeaders($this->headers), $this->proxy);
    }

}
