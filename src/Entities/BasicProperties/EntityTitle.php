<?php
namespace Avetify\Entities\BasicProperties;

interface EntityTitle {
    public function getItemTitle($record) : string;
}
