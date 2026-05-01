<?php
namespace Avetify\Table\Fields\ImageFields;

use Avetify\Fields\StructuredRecordValueField;

class ExtendedAvatarField extends TableAvatarField {
    use StructuredRecordValueField;

    public function __construct(string $title, string $key, string $imageWidth, string $structure) {
        $this->structure = $structure;
        parent::__construct($title, $key, $imageWidth);
    }

    public function getSrc($item) : string {
        return $this->getDerivedValue($item);
    }
}
