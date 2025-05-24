<?php
namespace Avetify\Table\Fields;

use Avetify\Interface\Styler;

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

class ExtendedAvatarField extends TableAvatarField {
    const DYNAMIC_IDENTIFIER = "*$*";

    public function __construct(string $title, string $key, string $imageWidth, public string $structure) {
        parent::__construct($title, $key, $imageWidth);
    }

    public function getSrc($item) : string {
        $dynamicPart = $this->getValue($item);
        $fullSrc = $this->structure;
        if(str_contains($fullSrc, self::DYNAMIC_IDENTIFIER)){
            $fullSrc = str_replace(self::DYNAMIC_IDENTIFIER, $dynamicPart, $fullSrc);
        }
        return $fullSrc;
    }
}
