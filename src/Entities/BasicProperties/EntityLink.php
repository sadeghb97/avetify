<?php
namespace Avetify\Entities\BasicProperties;

interface EntityLink {
    public function getItemLink($record) : string;
}
