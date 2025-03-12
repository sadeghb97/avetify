<?php

interface IDGetter {
    public function getID($item) : string;
}

class SimpleIDGetter implements IDGetter {
    public function __construct(public string $idKey){}

    public function getID($item): string {
        return EntityUtils::getSimpleValue($item, $this->idKey);
    }
}
