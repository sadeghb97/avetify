<?php
namespace Avetify\Forms;

class FormHiddenProperty {
    public function __construct(public string $hiddenPropertyId,
                                public string $value,
                                public bool $useId = true,
                                public $useName = true
    ){
    }
}
