<?php

/*** Implements EntityManager */
trait EntityManagerTrait {
    public function getItemId($record) : string {
        if($record instanceof EntityProfile) return $record->getItemId();
        return EntityUtils::getMultiChoiceValue($record, ["id", "pk", "slug"]);
    }

    public function getItemTitle($record) : string {
        if($record instanceof EntityProfile) return $record->getItemTitle();
        return EntityUtils::getMultiChoiceValue($record, ["name", "title"]);
    }

    public function getItemImage($record) : string {
        if($record instanceof EntityProfile) return $record->getItemImage();
        return EntityUtils::getMultiChoiceValue($record, ["image", "avatar"]);
    }

    public function getItemLink($record) : string {
        if($record instanceof EntityProfile) return $record->getItemLink();
        return EntityUtils::getMultiChoiceValue($record, ["url", "link"]);
    }

    public function getItemAltLink($record) : string {
        if($record instanceof EntityProfile) return $record->getItemAltLink();
        return "";
    }
}

/*** Implements EntityProfile */
trait EntityProfileTrait {
    public function getItemId(): string {
        if(property_exists($this, "id")) return $this->id;
        if(property_exists($this, "pk")) return $this->pk;
        if(property_exists($this, "slug")) return $this->slug;
        return "";
    }

    public function getItemTitle(): string {
        if(property_exists($this, "name")) return $this->name;
        if(property_exists($this, "title")) return $this->title;
        return "";
    }

    public function getItemAltLink(): string {
        return "";
    }
}
