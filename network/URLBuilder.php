<?php

class URLBuilder {
    public array $params = [];

    public function __construct(public string $baseUrl){
    }

    public function addParam($param, $paramValue){
        $this->params[$param] = $paramValue;
    }

    public function buildUrl() : string {
        if(count($this->params) == 0) return $this->baseUrl;
        $out = "";
        foreach ($this->params as $param => $paramValue){
            if($out) $out .= "&amp;";
            $out .= ($param . "=" . $paramValue);
        }
        return $this->baseUrl . "?" . $out;
    }
}
