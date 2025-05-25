<?php
namespace Avetify\Fields\JSTextFields;

use Avetify\Interface\WebModifier;

class APIACTextField extends APITextField {
    public function __construct(string $fieldKey, string $childKey,
                                string $initValue, string $endpoint, string $listId){
        parent::__construct($fieldKey, $childKey, $initValue, $endpoint);
        $this->listIdentifier = $listId;
    }

    public function place(?WebModifier $webModifier = null){
        $this->presentListField($webModifier);
    }
}
