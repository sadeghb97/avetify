<?php
namespace Avetify\Entities\Fields;

use Avetify\Entities\EntityField;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class EntityTextAreaField extends EntityField {
    public function __construct($key, $title, public bool $setMode = false) {
        parent::__construct($key, $title);
    }

    public function presentWritableField($item, ?WebModifier $webModifier = null) {
        $title = $this->title;
        $key = $this->key;
        $value = $item[$key] ?? null;

        if($this->setMode){
            $value = $value ? implode("\n", $value) : "";
        }

        echo '<span style="font-weight: 14;">' . $title . '</span><br>';
        echo '<textarea ';
        Styler::startAttribute();
        Styler::addStyle("margin-bottom", "8px");
        Styler::closeAttribute();
        HTMLInterface::addAttribute("name", $key);
        HTMLInterface::addAttribute("id", $key);
        HTMLInterface::addAttribute("rows", "8");
        HTMLInterface::addAttribute("cols", "150");
        HTMLInterface::closeTag();
        echo $value;
        echo '</textarea>';
    }
}
