<?php
namespace Avetify\Fields\Buttons;

use Avetify\DB\DBConnection;
use Avetify\Fields\BaseRecordField;
use Avetify\Fields\DateFields\DateValueType;
use Avetify\Fields\Links\LinkFieldTrait;
use Avetify\Fields\StructuredRecordValueField;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;

class IconJSField extends BaseRecordField {
    use StructuredRecordValueField;

    public function __construct(string $key, string $title, public string $imgSrc) {
        parent::__construct($key, $title);
        $this->baseModifier = $this->baseModifier ?? WebModifier::createInstance();
        $this->baseModifier->pushClass("avt_img_icon_buttons");
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        $rawValue = $this->getValue($item);
        $onTriggerJs = $this->getDerivedDirectValue($rawValue);
        $finalModifier = $this->getFinalModifier($webModifier);
        $finalModifier->pushModifier("onclick", $onTriggerJs);

        HTMLInterface::placeImageWithHeight($this->imgSrc, 50, $finalModifier);
    }
}