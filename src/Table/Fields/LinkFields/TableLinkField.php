<?php
namespace Avetify\Table\Fields\LinkFields;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\TableField;

abstract class TableLinkField extends TableField {
    public null | string $color = "Black";
    public bool $isBlank = false;

    public function __construct(string $title, string $key){
        parent::__construct($title, $key);
    }

    abstract function getLinkValue($item) : string;

    public function presentValue($item, ?WebModifier $webModifier = null){
        $title = $this->getValue($item);
        $link = $this->getLinkValue($item);

        if($link) {
            echo '<a href="' . $link . '" style="';
            if ($this->color != null) Styler::addStyle("color", $this->color);
            HTMLInterface::appendStyles($webModifier);
            echo '" ';

            Styler::classStartAttribute();
            HTMLInterface::appendClasses($webModifier);
            Styler::closeAttribute();

            if($this->isBlank) HTMLInterface::addAttribute("target", "_blank");
            HTMLInterface::applyModifiers($webModifier);
            HTMLInterface::closeTag();
        }

        echo $title;

        if($link) echo '</a>';
    }

    public function setBlank() : TableField {
        $this->isBlank = true;
        return $this;
    }
}
