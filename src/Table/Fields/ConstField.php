<?php
namespace Avetify\Table\Fields;

class ConstField {
    public function __construct(public string $key, public string $value, public bool $isNumeric){}
}