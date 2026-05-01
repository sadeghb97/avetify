<?php
namespace Avetify\Fields;

trait StructuredRecordValueField {
    public string $structure = "";

    public function setStructure(string $structure) : static {
        $this->structure = $structure;
        return $this;
    }

    public function getDerivedDirectValue(string $dynamicPart) : string {
        if(!$this->structure) return $dynamicPart;

        $fullDerivedValue = $this->structure;
        if(str_contains($fullDerivedValue, BaseRecordField::DYNAMIC_IDENTIFIER)){
            $fullDerivedValue = str_replace(BaseRecordField::DYNAMIC_IDENTIFIER, $dynamicPart, $fullDerivedValue);
        }
        return $fullDerivedValue;
    }

    public function getDerivedValue($item) : string {
        if(!($this instanceof BaseRecordField)) return "";
        $dynamicPart = $this->getValue($item);
        return $this->getDerivedDirectValue($dynamicPart);
    }
}
