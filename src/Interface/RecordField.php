<?php
namespace Avetify\Interface;

interface RecordField {
    public function getValue($item) : string;
    public function presentValue($item, ?WebModifier $webModifier = null);
}
