<?php
namespace Avetify\Fields\DateFields;

use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\WebModifier;
use Avetify\Themes\Main\AvtTheme;

class JalaliDatePicker extends DatePicker {
    public function presentValue($item, ?WebModifier $webModifier = null) : void {
        $time = $this->getTimeValue($item);

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
        HTMLInterface::addAttribute("data-target", $this->getElementIdentifier($item));
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

        if($this->printUnix) {
            echo '<div ';
            HTMLInterface::addAttribute("id", $this->getUnixSpanIdentifier($item));
            Styler::classStartAttribute();
            Styler::addClass('avt-timepicker__output');
            Styler::closeAttribute();
            HTMLInterface::closeTag();
            HTMLInterface::closeDiv();
        }

        HTMLInterface::closeDiv();

        $this->initJs($item);
    }

    public function getInitJsString($item) : string {
        $time = $this->getTimeValue($item);
        if($time === null) $time = "null";

        return "initJalaliField('" . $this->getElementIdentifier($item) . "', "
            . ($this->timeEnabled ? "true" : "false") . ", " . $time . ");";
    }

    public function attachRequirementsToTheme(AvtTheme $theme): AvtTheme {
        $theme = parent::attachRequirementsToTheme($theme);
        $theme->includesCommonDateTimeUtils = true;
        $theme->includesJQuery = true;
        $theme->includesJalaliDateTimeUtils = true;
        return $theme;
    }
}