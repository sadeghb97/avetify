<?php
namespace Avetify\Table\Fields\EditableFields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;

class CheckboxField extends EditableField {
    public bool $isNumeric = true;
    public function presentValue($item, ?WebModifier $webModifier = null){
        $div = new NiceDiv(8);
        $div->open($webModifier);

        if($this->isLabelEnabled){
            echo '<div ';
            HTMLInterface::closeTag();
            echo $this->title;
            HTMLInterface::closeDiv();
            $div->separate();
        }

        if($this->useNameIdentifier){
            echo '<input ';
            HTMLInterface::addAttribute("type", "hidden");
            HTMLInterface::addAttribute("name", $this->getElementIdentifier($item));
            HTMLInterface::addAttribute("value", "0");
            HTMLInterface::closeTag();
        }

        echo '<input ';
        $this->appendMainAttributes($item);
        HTMLInterface::addAttribute("type", "checkbox");
        HTMLInterface::addAttribute("value", "1");

        $checked = $item != null && !!$this->getValue($item);
        if($checked) HTMLInterface::addAttribute("checked");

        Styler::startAttribute();
        $this->appendMainStyles($item);
        Styler::closeAttribute();

        HTMLInterface::closeSingleTag();

        $div->close();
    }
}
