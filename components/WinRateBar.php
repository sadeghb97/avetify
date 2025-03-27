<?php

class WinRateBar implements Placeable {
    public int $count = 0;
    public int $radius = 0;
    public int $padding = 0;

    public function __construct(public int $height, public int $wins, public int $draws, public int $loses) {
        $this->count = $this->wins + $this->draws + $this->loses;
        $this->radius = (int)($this->height / 5);
        $this->padding = (int)($this->height / 4);
    }

    public function place(WebModifier $webModifier = null) {
        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle("width", "100%");
        Styler::addStyle("height", $this->height . "px");
        Styler::addStyle("display", "flex");
        Styler::addStyle("margin-top", "2px");
        Styler::addStyle("margin-bottom", "2px");
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        if($this->wins > 0) {
            $percentage = $this->wins / $this->count * 100;

            echo '<div ';
            Styler::startAttribute();
            Styler::addStyle("width", "$percentage%");
            Styler::addStyle("height", $this->height . "px");
            Styler::addStyle("background-color", "green");
            $this->roundLeftStyles();
            if($this->draws == 0 && $this->loses == 0) $this->roundRightStyles();
            Styler::closeAttribute();
            HTMLInterface::closeTag();

            if($percentage > 3) {
                echo '<div ';
                Styler::startAttribute();
                Styler::addStyle("padding-top", $this->padding . "px");
                Styler::closeAttribute();
                HTMLInterface::closeTag();
                echo $this->wins;
                HTMLInterface::closeDiv();
            }

            HTMLInterface::closeDiv();
        }

        if($this->draws > 0) {
            $percentage = $this->draws / $this->count * 100;

            echo '<div ';
            Styler::startAttribute();
            Styler::addStyle("width", "$percentage%");
            Styler::addStyle("height", "100%");
            Styler::addStyle("background-color", "grey");
            if ($this->wins == 0) $this->roundLeftStyles();
            if ($this->loses == 0) $this->roundRightStyles();
            Styler::closeAttribute();
            HTMLInterface::closeTag();

            if($percentage > 3) {
                echo '<div ';
                Styler::startAttribute();
                Styler::addStyle("padding-top", $this->padding . "px");
                Styler::closeAttribute();
                HTMLInterface::closeTag();
                echo $this->draws;
                HTMLInterface::closeDiv();
            }

            HTMLInterface::closeDiv();
        }

        if($this->loses > 0) {
            $percentage = $this->loses / $this->count * 100;

            echo '<div ';
            Styler::startAttribute();
            Styler::addStyle("width", "$percentage%");
            Styler::addStyle("height", "100%");
            Styler::addStyle("background-color", "red");
            if ($this->wins == 0 && $this->draws == 0) $this->roundLeftStyles();
            $this->roundRightStyles();
            Styler::closeAttribute();
            HTMLInterface::closeTag();

            if($percentage > 3) {
                echo '<div ';
                Styler::startAttribute();
                Styler::addStyle("padding-top", $this->padding . "px");
                Styler::closeAttribute();
                HTMLInterface::closeTag();
                echo $this->loses;
                HTMLInterface::closeDiv();
            }

            HTMLInterface::closeDiv();
        }

        HTMLInterface::closeDiv();
    }

    public function roundLeftStyles(){
        Styler::addStyle("border-top-left-radius", $this->radius . "px");
        Styler::addStyle("border-bottom-left-radius", $this->radius . "px");
    }

    public function roundRightStyles(){
        Styler::addStyle("border-top-right-radius", $this->radius . "px");
        Styler::addStyle("border-bottom-right-radius", $this->radius . "px");
    }
}
