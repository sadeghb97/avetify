<?php

class URLBuilder {
    public array $params = [];

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

    public function buildUrl(string $newBaseUrl = null) : string {
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
