<?php
namespace Avetify\Table\Fields\ImageFields;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Table\Fields\TableField;

class TableAvatarField extends TableField {
    public function __construct(string $title, string $key, public string $imageWidth){
        parent::__construct($title, $key);
    }

    public function getSrc($item) : string {
        return $this->getValue($item);
    }

    public function presentValue($item, ?WebModifier $webModifier = null){
        $image = $this->getSrc($item);
        echo '<img src="' . $image . '" style="';
        if($this->imageWidth){
            Styler::addStyle("width", $this->imageWidth);
            Styler::addStyle("height", "auto");
        }
        HTMLInterface::appendStyles($webModifier);
        echo '" ';

        Styler::classStartAttribute();
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();

        HTMLInterface::applyModifiers($webModifier);
        HTMLInterface::closeSingleTag();
    }
}
