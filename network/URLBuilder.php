<?php

class URLBuilder {
    public array $params = [];

    public static function fromCurrent() : URLBuilder {
        $urlBuilder = new URLBuilder();
        $urlBuilder->params = array_merge([], $_GET);
        return $urlBuilder;
    }

    public static function fromUrl(string $url) : URLBuilder {
        $urlBuilder = new URLBuilder();
        $urlParams = Routing::getUrlQuery($url);
        parse_str($urlParams, $queryParams);
        $urlBuilder->params = $queryParams;
        return $urlBuilder;
    }

    public function __construct(public null | string $baseUrl = null){
    }

    public function addParam($param, $paramValue) : URLBuilder {
        $this->params[$param] = $paramValue;
        return $this;
    }

    public function getClone() : URLBuilder {
        $urlBuilder = new URLBuilder($this->baseUrl);
        $urlBuilder->params = array_merge([], $this->params);
        return $urlBuilder;
    }

    public function buildUrl(string $newBaseUrl = null, array $moreParams = []) : string {
        if($newBaseUrl){
            $nbuParams = Routing::getUrlParams($newBaseUrl);
            $nbuPure = Routing::getUrlPureLink($newBaseUrl);

            $newBaseUrl = $nbuPure;
            $moreParams = array_merge($moreParams, $nbuParams);
        }

        if($moreParams && count($moreParams) > 0){
            $cloneBuilder = $this->getClone();
            foreach ($moreParams as $paramKey => $paramValue){
                $cloneBuilder->addParam($paramKey, $paramValue);
            }
            return $cloneBuilder->buildUrl($newBaseUrl);
        }
        if($newBaseUrl) $this->baseUrl = $newBaseUrl;

        if(count($this->params) == 0) return $this->baseUrl;
        $out = "";
        foreach ($this->params as $param => $paramValue){
            if($out) $out .= "&amp;";
            $out .= ($param . "=" . $paramValue);
        }

        if($this->baseUrl) return $this->baseUrl . "?" . $out;
        return $out;
    }
}
