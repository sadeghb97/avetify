<?php
namespace Avetify\Entities\BasicProperties\Traits;

use Avetify\Entities\BasicProperties\EntityProfile;
use Avetify\Entities\EntityUtils;

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

    public function getItemTags($record) : array {
        if($record instanceof EntityProfile) return $record->getItemTags();
        return [];
    }

    public function getItemDescription($record) : string {
        if($record instanceof EntityProfile) return $record->getItemDescription();
        return "";
    }
}
