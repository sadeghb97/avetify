<?php
namespace Avetify\Fields\JSTextFields;

class APITextField extends JSTextField {
    public function __construct(string $fieldKey, string $childKey, string $initValue,
                                public string $apiEndpoint){
        parent::__construct($fieldKey, $childKey, $initValue);
    }

    public function applyText() : string {
        return 'apiTextEnterAction(\'' . $this->getElementIdentifier() . '\', \'' . $this->childKey .
            '\', \'' . $this->fieldKey . '\', \'' .
            $this->apiEndpoint . '\', ' . $this->applyTextCallback() . ');';
    }

    public function applyTextCallback() : string {
        return "(data) => {" .
            "console.log('DATA', data)" .
            "}";
    }
}
