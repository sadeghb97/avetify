<?php

namespace Avetify\Games\AvtTrivia;

use Avetify\Entities\AvtEntityItem;

abstract class AvtTriviaItem extends AvtEntityItem {
    public function getDifficulty() : int {
        if(property_exists($this, "difficulty") && is_numeric($this->difficulty)) return $this->difficulty;
        return 1;
    }
}