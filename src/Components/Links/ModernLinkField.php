<?php
namespace Avetify\Components\Links;

use Avetify\Fields\StructuredRecordValueField;
use Avetify\Interface\Bootstrap\PlatformIcons;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\IdentifiedElement;
use Avetify\Interface\IdentifiedElementTrait;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;

class ModernLinkField implements Placeable, IdentifiedElement {
    use IdentifiedElementTrait;
    use StructuredRecordValueField;

    public string $icon = PlatformIcons::GLOBE;
    public WebModifier | null $labelModifier = null;

    public function __construct(public string $label, public string $key, public string $initValue){
        $this->initValue = trim($this->initValue);
    }

    public function place(WebModifier $webModifier = null) {
        echo '<div ';
        $webModifier?->htmlModifier->applyModifiers();
        Styler::classStartAttribute();
        Styler::addClass("link-field");
        $webModifier?->styler->appendClasses();
        Styler::closeAttribute();

        Styler::startAttribute();
        $webModifier?->styler->appendStyles();
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        echo '<div class="form-floating">';

        echo '<input ';
        Styler::classStartAttribute();
        Styler::addClass("form-control");
        Styler::addClass("link-field-input");
        Styler::closeAttribute();
        $this->placeElementIdAttributes();
        HTMLInterface::addAttribute("type", "url");
        HTMLInterface::addAttribute("placeholder", $this->label);
        HTMLInterface::addAttribute("value", htmlspecialchars($this->initValue));
        HTMLInterface::closeTag();

        echo '<label ';
        HTMLInterface::addAttribute("for", $this->getElementIdentifier());
        $webModifier?->apply();
        HTMLInterface::closeTag();
        echo $this->label;
        echo '</label>';

        HTMLInterface::closeDiv();

        if($this->initValue) {
            $finalDerivedLink = $this->getDerivedDirectValue($this->initValue);
            echo '<a ';
            Styler::classStartAttribute();
            Styler::addClass("link-icon");
            Styler::closeAttribute();
            HTMLInterface::addAttribute("href", $finalDerivedLink);
            HTMLInterface::addAttribute("target", "_blank");
            HTMLInterface::closeTag();
        }

        echo '<i ';
        Styler::classStartAttribute();
        if(!$this->initValue) Styler::addClass("link-icon");
        Styler::addClass("bi");
        Styler::addClass($this->icon);
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo '</i>';

        if($this->initValue) {
            echo '</a>';
        }


        HTMLInterface::closeDiv();
    }

    public function getElementIdentifier($item = null) : string {
        return $this->key;
    }
}