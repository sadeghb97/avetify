<?php
namespace Avetify\Table\Fields\EditableFields;

use Avetify\Components\Links\ModernLinkField;
use Avetify\Interface\Bootstrap\PlatformIcons;
use Avetify\Interface\WebModifier;

class EditableLinkField extends EditableField {
    public ModernLinkField $modernLinkField;
    public string $icon = PlatformIcons::GLOBE;
    public WebModifier | null $labelModifier = null;

    public function presentValue($item, ?WebModifier $webModifier = null){
        $value = $this->getValue($item);
        $this->modernLinkField = new ModernLinkField($this->title, $this->getElementIdentifier($item), $value);
        $this->modernLinkField->icon = $this->icon;
        $this->modernLinkField->labelModifier = $this->labelModifier;
        $this->modernLinkField->place($webModifier);
    }

    public function setIcon(string $icon) : static {
        $this->icon = $icon;
        return $this;
    }

    public function setLabelModifier(WebModifier $modifier) : static {
        $this->labelModifier = $modifier;
        return $this;
    }
}
