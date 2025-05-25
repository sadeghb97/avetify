<?php
namespace Avetify\Entities\BasicProperties;

interface EntityID {
    public function getItemId($record) : string;
}
