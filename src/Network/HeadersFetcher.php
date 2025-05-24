<?php
namespace Avetify\Network;

class HeadersFetcher extends NetworkFetcher {
    public function __construct(public string $headers){}

    public function fetch($url): string{
        return $this->fetchUrlWithHeaders($url, self::parseRawHeaders($this->headers), null);
    }
}
