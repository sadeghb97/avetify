<?php
namespace Avetify\Table\Fields\ImageFields;

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
