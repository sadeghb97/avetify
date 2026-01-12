<?php
namespace Avetify\Entities\Fields;

use Avetify\Components\Coding\CodingField;
use Avetify\Entities\EntityField;
use Avetify\Interface\WebModifier;

class EntityCodingField extends EntityField {
    public const CodingFieldType = "coding_field";
    public string $defWrapper = "";

    public function postConstruct() {
        $this->type = self::CodingFieldType;
    }

    public function setWrapper(string $wrapper) : EntityCodingField {
        $this->defWrapper = $wrapper;
        return $this;
    }

    public function presentWritableField($item, ?WebModifier $webModifier = null) {
        $key = $this->key;
        $value = $this->getValue($item);

        $codingField = new CodingField($this->title, $this->key, $value, ucfirst($this->defWrapper));
        $codingField->place();
    }
}
