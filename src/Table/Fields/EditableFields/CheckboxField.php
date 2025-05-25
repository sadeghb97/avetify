<?php
namespace Avetify\Table\Fields\EditableFields;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;

class CheckboxField extends EditableField {
    public bool $isNumeric = true;
    public function presentValue($item){
        echo '<input ';
        HTMLInterface::addAttribute("type", "checkbox");
        if($item != null) {
            $checked = !!$this->getValue($item);
            if($checked) HTMLInterface::addAttribute("checked");
        }
        $this->appendMainAttributes($item);
        Styler::startAttribute();
        $this->appendMainStyles($item);
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();
    }
}
