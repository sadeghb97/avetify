<?php

abstract class SBEntityItem extends DataModel {
    public static function createInstance(string $className, array $data): SBEntityItem {
        if (!is_subclass_of($className, SBEntityItem::class)) {
            throw new InvalidArgumentException("$className must extend SBEntityItem");
        }

        return new $className($data);
    }

    /** @return SBEntityItem[] */
    public static function mapArray(string $className, array $records) : array {
        $out = [];
        foreach ($records as $record){
            $out[] = self::createInstance($className, $record);
        }
        return $out;
    }

    abstract public function deleteAllResources();
    abstract public function getAvatarSrc() : string;
}
