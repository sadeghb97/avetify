<?php
namespace Avetify\Entities;

use Avetify\Models\DataModel;
use Avetify\Models\Traits\Tagged;
use InvalidArgumentException;

abstract class AvtEntityItem extends DataModel implements EntityProfile {
    use Tagged;
    use EntityProfileTrait;

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

    abstract public function deleteAllResources();
}
