<?php

class HeadersFetcher extends NetworkFetcher {
    public function __construct(public string $headers){}

    public function fetch($url): string{
        return fetchUrlWithHeaders($url, parseRawHeaders($this->headers), null);
    }
}
