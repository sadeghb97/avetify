<?php
namespace Avetify\Entities\BasicProperties\Traits;

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

    public function getItemFaTitle(): string {
        if(property_exists($this, "per_name")) return $this->per_name;
        if(property_exists($this, "fa_name")) return $this->fa_name;
        if(property_exists($this, "per_title")) return $this->per_title;
        if(property_exists($this, "fa_title")) return $this->fa_title;
        return $this->getItemTitle();
    }

    public function getItemAltLink(): string {
        return "";
    }

    public function getItemDescription(): string {
        if(property_exists($this, "description")) return $this->description;
        return "";
    }

    public function getItemTags(): array {
        return [];
    }

    public function getItemLink() : string {
        if(property_exists($this, "link")) return $this->link;
        return "";
    }

    public function getItemImage() : string {
        if(property_exists($this, "image")) return $this->image;
        return "";
    }
}
