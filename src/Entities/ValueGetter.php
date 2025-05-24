<?php
namespace Avetify\Entities;

interface ValueGetter {
    public function getValue($item) : string | float;
}
