<?php
namespace Avetify\Network;

class ProxyFetcher extends NetworkFetcher {
    public function __construct(string $proxy){
        $this->proxy = $proxy;
    }
}
