<?php
namespace Avetify\Entities\BasicProperties;

interface EntityDescription {
    public function getItemDescription($record) : string;
}
