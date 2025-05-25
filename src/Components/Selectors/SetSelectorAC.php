<?php
namespace Avetify\Components\Selectors;

use Avetify\Fields\JSDatalist;
use Avetify\Fields\JSTextFields\JSACTextField;

class SetSelectorAC extends JSACTextField {
    public bool $disableSubmitOnEnter = true;
    public bool $tinyAvatars = false;

    public function __construct(string $fieldKey, string $childKey, string $initValue,
                                JSDatalist $dlInfo, public SetSelector $selector) {
        parent::__construct($fieldKey, $childKey, $initValue, $dlInfo);
        $this->enterCallbackName = "addRecordToSelector";
    }

    public function callbackMoreData(): array {
        return $this->selector->selectorMoreData();
    }
}
