<?php
namespace Avetify\Entities\Fields;

use Avetify\Entities\EntityField;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class EntityBooleanField extends EntityField {
    public function presentWritableField($item, ?WebModifier $webModifier = null) {
        $title = $this->title;
        $key = $this->key;
        $value = $this->getValue($item);

        echo $title . ' ';
        echo '<input ';
        HTMLInterface::addAttribute("type", "checkbox");
        HTMLInterface::addAttribute("value", "1");
        $this->placeElementIdAttributes();
        if($value) HTMLInterface::addAttribute("checked", "true");
        Styler::startAttribute();
        Styler::addStyle("margin-bottom", "8px");
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();
    }
}
