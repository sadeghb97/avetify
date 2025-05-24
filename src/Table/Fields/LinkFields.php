<?php
namespace Avetify\Table\Fields;

use Avetify\Entities\EntityUtils;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;

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

class SBTableSimpleLinkField extends TableLinkField {
    public function __construct(string $title, string $key, public string $linkKey){
        parent::__construct($title, $key);
    }

    public function getValue($item): string {
        return EntityUtils::getSimpleValue($item, $this->key);
    }

    function getLinkValue($item): string {
        return EntityUtils::getSimpleValue($item, $this->linkKey);
    }
}

class TableMainLinkField extends TableLinkField {
    function getLinkValue($item): string {
        return $item->getItemLink();
    }
}

class TableAltLinkField extends TableLinkField {
    function getLinkValue($item): string {
        return $item->getItemAltLink();
    }
}

