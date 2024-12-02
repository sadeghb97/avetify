<?php

interface EntityTitle {
    public function getItemTitle($record) : string;
}

interface EntityID {
    public function getItemId($record) : string;
}

interface EntityImage {
    public function getItemImage($record) : string;
}

interface EntityLink {
    public function getItemLink($record) : string;
}

trait DefaultEntityTitle {
    public function getItemTitle($record) : string {
        return EntityUtils::getSimpleValue($record, ["name", "title"]);
    }
}

trait DefaultEntityID {
    public function getItemId($record) : string {
        return EntityUtils::getSimpleValue($record, ["id", "pk"]);
    }
}

trait DefaultEntityImage {
    public function getItemImage($record) : string {
        return EntityUtils::getSimpleValue($record, ["image", "avatar"]);
    }
}

trait DefaultEntityLink {
    public function getItemLink($record) : string {
        return EntityUtils::getSimpleValue($record, ["url", "link", "href"]);
    }
}
