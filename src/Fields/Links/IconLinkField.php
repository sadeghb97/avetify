<?php
namespace Avetify\Fields\Links;

use Avetify\DB\DBConnection;
use Avetify\Fields\BaseRecordField;
use Avetify\Fields\DateFields\DateValueType;
use Avetify\Fields\StructuredRecordValueField;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;

abstract class IconLinkField extends BaseRecordField {
    use StructuredRecordValueField;
    use LinkFieldTrait;

    public function __construct(string $key, string $title, public string $imgSrc) {
        parent::__construct($key, $title);
    }

    public function presentValue($item, ?WebModifier $webModifier = null) {
        $rawValue = $this->getValue($item);
        $finalDerivedLink = $this->getDerivedDirectValue($rawValue);
        $finalModifier = $this->getFinalModifier($webModifier);

        echo '<a ';
        HTMLInterface::addAttribute('href', $finalDerivedLink);
        if($this->isBlank) HTMLInterface::addAttribute('target', "_blank");
        HTMLInterface::closeTag();

        HTMLInterface::placeImageWithHeight($this->imgSrc, 60, $finalModifier);
        echo '</a>';
    }
}