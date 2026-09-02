<?php
namespace Avetify\Entities;

use Avetify\Entities\BasicProperties\EntityProfile;
use Avetify\Entities\BasicProperties\Traits\EntityProfileTrait;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\Platform;
use Avetify\Interface\WebModifier;
use Avetify\Models\DataModel;
use Avetify\Models\Traits\Tagged;
use InvalidArgumentException;

abstract class AvtEntityItem extends DataModel implements EntityProfile {
    use Tagged;
    use EntityProfileTrait;

    public int $created_at = 0;
    public int $updated_at = 0;

    public static function createInstance(string $className, array $data): AvtEntityItem {
        if (!is_subclass_of($className, AvtEntityItem::class)) {
            throw new InvalidArgumentException("$className must extend AvtEntityItem");
        }

        return new $className($data);
    }

    /** @return AvtEntityItem[] */
    public static function mapArray(string $className, array $records) : array {
        $out = [];
        foreach ($records as $record){
            $out[] = self::createInstance($className, $record);
        }
        return $out;
    }

    public function placeLink(bool $blank = true, ?WebModifier $modifier = null) : void {
        if(!Platform::isCli()) {
            $insertLinkModifier = $modifier ?? WebModifier::createInstance();
            if($blank) $insertLinkModifier->pushModifier("target", "_blank");
            $insertLinkModifier->pushStyle("display", "contents");
            $insertLinkModifier->pushStyle("font-weight", "bold");
            HTMLInterface::placeLink($this->getItemLink(), $this->getItemTitle(), $insertLinkModifier);
        }
        else {
            echo $this->getItemTitle() . ' (' . $this->getItemLink() . ')';
        }
    }

    abstract public function deleteAllResources();
}
