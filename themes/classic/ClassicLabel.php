<?php

class ClassicLabel implements Placeable {
    public function __construct(public string $label, public string $src,
                                public string $backgroundColor='black',
                                public string $color='Cyan'){}

    public function place(WebModifier $webModifier = null) {
        echo '<a ';
        Styler::startAttribute();
        Styler::addStyle(CSS::textDecoration, "none");
        Styler::closeAttribute();
        HTMLInterface::addAttribute(Attrs::href, $this->src);
        HTMLInterface::closeTag();

        $labelModifier = WebModifier::createInstance();
        $labelModifier->styler->pushClass("label");
        $labelModifier->styler->pushStyle(CSS::backgroundColor, $this->backgroundColor);
        $labelModifier->styler->pushStyle(CSS::color, $this->color);
        $labelModifier->styler->pushStyle(CSS::display,"inline-block");
        $labelModifier->styler->pushStyle(CSS::margin, "1.5px");
        HTMLInterface::placeSpan($this->label, $labelModifier);

        echo '</a>';
    }
}
