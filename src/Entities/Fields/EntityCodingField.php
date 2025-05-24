<?php
namespace Avetify\Entities\Fields;

use Avetify\Components\CodingField;
use Avetify\Entities\EntityField;
use Avetify\Entities\EntityUtils;
use Avetify\Interface\EntityView;
use Avetify\Interface\WebModifier;

class EntityCodingField extends EntityField implements EntityView {
    public const CodingFieldType = "coding_field";
    public string $defWrapper = "";

    public function postConstruct() {
        $this->type = self::CodingFieldType;
    }

    public function place($record, ?WebModifier $modifier = null) {
        $value = EntityUtils::getSimpleValue($record, $this->key);
        $codingField = new CodingField($this->title, $this->key, $value, ucfirst($this->defWrapper));
        $codingField->place();
    }

    public function setWrapper(string $wrapper) : EntityCodingField {
        $this->defWrapper = $wrapper;
        return $this;
    }
}
