<?php
namespace Avetify\Table\Fields\LinkFields;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Table\Fields\TableField;

abstract class TableLinkField extends TableField {
    public null | string $color = "Black";
    public bool $isBlank = false;

    public function __construct(string $title, string $key){
        parent::__construct($title, $key);
    }

    abstract function getLinkValue($item) : string;

    public function presentValue($item){
        $title = $this->getValue($item);
        $link = $this->getLinkValue($item);
        echo '<a href="' . $link . '" style="';
        if($this->color != null) Styler::addStyle("color", $this->color);
        echo '" ';
        if($this->isBlank) HTMLInterface::addAttribute("target", "_blank");
        HTMLInterface::closeTag();
        echo $title;
        echo '</a>';
    }

    public function setBlank() : TableField {
        $this->isBlank = true;
        return $this;
    }
}
