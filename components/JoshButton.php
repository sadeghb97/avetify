<?php

class JoshButton {
    public function __construct(public string $title, public string $buttonId,
                                public string $buttonStyle,
                                public string $buttonType = "button"){
    }

    public function renderButton(WebModifier $webModifier = null){
        $buttonStyle = "pushable";
        if($this->buttonStyle == "warning") $buttonStyle .= (' ' . $this->buttonStyle);

        echo '<button ';
        HTMLInterface::addAttribute("type", $this->buttonType);
        if($this->buttonId) HTMLInterface::addAttribute("id", $this->buttonId);
        HTMLInterface::addAttribute("class", $buttonStyle);
        if($webModifier != null && $webModifier->htmlModifier != null){
            $webModifier->htmlModifier->applyModifiers();
        }
        if($webModifier != null && $webModifier->styler != null){
            $webModifier->styler->applyStyles();
        }
        HTMLInterface::closeTag();

        echo '<span ';
        HTMLInterface::addAttribute("class", "front");
        HTMLInterface::closeTag();
        echo $this->title;
        echo '</span>';

        echo '</button>';
    }
}