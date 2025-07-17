<?php
namespace Avetify\Entities\Models;

class EntityReceivedSort {
    public function __construct(public string $key, public bool $alterDirection) {}
}
