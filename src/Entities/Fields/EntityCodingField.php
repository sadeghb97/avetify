<?php
namespace Avetify\Entities\Fields;

use Avetify\Components\Coding\CodingField;
use Avetify\Entities\EntityField;
use Avetify\Interface\WebModifier;
use Avetify\Themes\Main\AvtTheme;

class EntityCodingField extends EntityField {
    public string $defWrapper = "";

    public function setWrapper(string $wrapper) : EntityCodingField {
        $this->defWrapper = $wrapper;
        return $this;
    }

    public function presentWritableField($item, ?WebModifier $webModifier = null) {
        $value = $this->getValue($item);

        $codingField = new CodingField($this->title, $this->key, $value, ucfirst($this->defWrapper));
        $codingField->place();
    }

    public function attachRequirementsToTheme(AvtTheme $theme): AvtTheme {
        $theme = parent::attachRequirementsToTheme($theme);
        $theme->includesCodingFieldTools = true;
        return $theme;
    }
}
