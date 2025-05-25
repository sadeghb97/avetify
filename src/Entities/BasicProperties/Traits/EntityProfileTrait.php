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

    public function getItemAltLink(): string {
        return "";
    }
}
