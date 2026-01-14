<?php
namespace Avetify\Entities\Fields;

use Avetify\Entities\EntityField;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\WebModifier;

class EntityHiddenField extends EntityField {
    public function __construct($key, $title) {
        parent::__construct($key, $title);
        $this->setHidden();
    }

    public function presentWritableField($item, ?WebModifier $webModifier = null) {
        $title = $this->title;
        $key = $this->key;
        $value = $this->getValue($item);

        echo '<input ';
        HTMLInterface::addAttribute("type","hidden");
        $this->placeElementIdAttributes();
        HTMLInterface::addAttribute("value", $value ? $value : "");
        HTMLInterface::closeSingleTag();
    }
}
