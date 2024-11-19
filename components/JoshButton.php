<?php

class JoshButton {
    public function __construct(public string $title, public string $buttonId, public string $buttonStyle){
    }

    public function renderButton(){
        $buttonStyle = "pushable";
        if($this->buttonStyle == "warning") $buttonStyle .= (' ' . $this->buttonStyle);

        echo '<button ';
        HTMLInterface::addAttribute("type", "button");
        HTMLInterface::addAttribute("id", $this->buttonId);
        HTMLInterface::addAttribute("class", $buttonStyle);
        HTMLInterface::closeTag();

        echo '<span ';
        HTMLInterface::addAttribute("class", "front");
        HTMLInterface::closeTag();
        echo $this->title;
        echo '</span>';

        echo '</button>';
    }
}