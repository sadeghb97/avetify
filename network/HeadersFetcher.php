<?php

class HeadersFetcher extends NetworkFetcher {
    public function __construct(public string $headers){}

    public function fetch($url): string{
        return self::fetchUrlWithHeaders($url, self::parseRawHeaders($this->headers), null);
    }
}
