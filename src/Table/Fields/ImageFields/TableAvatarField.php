<?php
namespace Avetify\Table\Fields\ImageFields;

use Avetify\Interface\Styler;
use Avetify\Table\Fields\TableField;

class TableAvatarField extends TableField {
    public function __construct(string $title, string $key, public string $imageWidth){
        parent::__construct($title, $key);
    }

    public function getSrc($item) : string {
        return $this->getValue($item);
    }

    public function presentValue($item){
        $image = $this->getSrc($item);
        echo '<img src="' . $image . '" style="';
        if($this->imageWidth){
            Styler::addStyle("width", $this->imageWidth);
            Styler::addStyle("height", "auto");
        }
        echo '" >';
    }
}
