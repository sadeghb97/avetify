<?php

abstract class SBEntityItem extends DataModel {
    public static function createInstance(string $className, array $data): SBEntityItem {
        if (!is_subclass_of($className, SBEntityItem::class)) {
            throw new InvalidArgumentException("$className must extend SBEntityItem");
        }

        return new $className($data);
    }

    abstract public function deleteAllResources();
    abstract public function getAvatarSrc() : string;
}
