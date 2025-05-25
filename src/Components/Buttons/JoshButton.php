<?php
namespace Avetify\Components\Buttons;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class JoshButton {
    public function __construct(public string $title, public string $buttonId,
                                public string $buttonStyle,
                                public string $buttonType = "button"){
    }

    public const WARNING_STYLE = "warning";

    public function renderButton(WebModifier $webModifier = null){
        $buttonStyle = "pushable";
        if($this->buttonStyle == "warning") $buttonStyle .= (' ' . $this->buttonStyle);

        echo '<button ';
        HTMLInterface::addAttribute("type", $this->buttonType);
        if($this->buttonId) HTMLInterface::addAttribute("id", $this->buttonId);
        HTMLInterface::addAttribute("onkeydown", 'return false;');
        Styler::classStartAttribute();
        Styler::addClass($buttonStyle);
        HTMLInterface::appendClasses($webModifier);
        Styler::closeAttribute();
        HTMLInterface::applyModifiers($webModifier);
        Styler::startAttribute();
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        echo '<span ';
        HTMLInterface::addAttribute("class", "front");
        HTMLInterface::closeTag();
        echo $this->title;
        echo '</span>';

        echo '</button>';
    }
}