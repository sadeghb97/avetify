<?php
namespace Avetify\Network;

class ProxyHeadersFetcher extends ProxyFetcher {
    public function __construct(string $proxy, public string $headers){
        parent::__construct($proxy);
    }

    function fetch($url) : string {
        return $this->fetchUrlWithHeaders($url, self::parseRawHeaders($this->headers), $this->proxy);
    }

}
