<?php
namespace Avetify\Table\Fields\EditableFields;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class CheckboxField extends EditableField {
    public bool $isNumeric = true;
    public function presentValue($item, ?WebModifier $webModifier = null){
        echo '<input ';
        HTMLInterface::addAttribute("type", "checkbox");
        if($item != null) {
            $checked = !!$this->getValue($item);
            if($checked) HTMLInterface::addAttribute("checked");
        }
        $this->appendMainAttributes($item);
        HTMLInterface::applyModifiers($webModifier);

        Styler::startAttribute();
        $this->appendMainStyles($item);
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();

        Styler::classStartAttribute();
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();

        HTMLInterface::closeSingleTag();
    }
}
