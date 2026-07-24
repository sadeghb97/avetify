<?php
namespace Avetify\Fields\DateFields;

use Avetify\Fields\BaseRecordField;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;

class JalaliDatePicker extends BaseRecordField {
    public bool $enableTime = false;
    public bool $enableUnix = false;

    public function presentValue($item, ?WebModifier $webModifier = null) : void {
        $time = intval($this->getValue($item));

        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass('avt-timepicker');
        Styler::addClass('avt-timepicker__row');
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        echo '<label ';
        HTMLInterface::closeTag();
        echo $this->title;
        echo '</label>';

        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass('avt-timepicker__field-row');
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        echo '<input ';
        HTMLInterface::addAttribute("type", "text");
        HTMLInterface::addAttribute("id", $this->getFieldInputIdentifier($item));
        Styler::classStartAttribute();
        Styler::addClass('avt-timepicker__jalali-input');
        Styler::addClass('avt-timepicker__input');
        Styler::closeAttribute();
        echo 'readonly ';
        HTMLInterface::addAttribute('placeholder', '----/--/--');
        HTMLInterface::closeTag();

        echo '<button ';
        HTMLInterface::addAttribute("type", "button");
        HTMLInterface::addAttribute("data-target", $this->enableTime ? "jalali_date" : "jalali_datetime");
        Styler::classStartAttribute();
        Styler::addClass('avt-timepicker__clear-button');
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo '×';
        echo '</button>';

        HTMLInterface::closeDiv();

        echo '<input ';
        HTMLInterface::addAttribute("type", "hidden");
        $this->placeElementIdAttributes($item);
        HTMLInterface::addAttribute('value', $time);
        HTMLInterface::closeTag();

        echo '<div ';
        HTMLInterface::addAttribute("id", $this->getUnixSpanIdentifier($item));
        Styler::classStartAttribute();
        Styler::addClass('avt-timepicker__output');
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        HTMLInterface::closeDiv();

        HTMLInterface::closeDiv();

        $this->initJs($item);
    }

    public function initJs($item) : void {
        echo '<script>';
        echo $this->getInitJsString($item);
        echo '</script>';
    }

    public function getInitJsString($item) : string {
        $time = intval($this->getValue($item));
        return "initJalaliField('" . $this->getElementIdentifier($item) . "', "
            . ($this->enableTime ? "true" : "false") . ", " . $time . ");";
    }

    public function getFieldInputIdentifier($item) : string {
        return $this->getElementIdentifier($item) . "_display";
    }

    public function getUnixSpanIdentifier($item) : string {
        return $this->getElementIdentifier($item) . "_unix";
    }
}