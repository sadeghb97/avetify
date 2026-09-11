<?php
namespace Avetify\Components\Containers;

use Avetify\Interface\AvtContainer;
use Avetify\Interface\CSS\Styler;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;

class NiceDiv implements AvtContainer {
    protected WebModifier $baseModifier;
    private int $itemsCount = 0;

    public static function justOpen(?WebModifier $webModifier = null) : void {
        $niceDiv = new NiceDiv(0);
        $niceDiv->open($webModifier);
    }

    public function __construct(public string $sepSize = "0px"){
        $this->initBaseModifier();
    }

    protected function initBaseModifier() : void {
        $this->baseModifier = WebModifier::createInstance();
        $this->baseModifier->pushClass("nice-div");
    }

    public function addStyle($key, $value) : void{
        $this->baseModifier->pushStyle($key, $value);
    }
    public function addClass($className) : void {
        $this->baseModifier->pushClass($className);
    }
    public function addModifier($key, $value) : void {
        $this->baseModifier->pushModifier($key, $value);
    }

    public function baseOpen(?WebModifier $webModifier = null) : void {
        $finalModifier = WebModifier::mergeModifiers($this->baseModifier, $webModifier);

        echo '<div ';
        $finalModifier->htmlModifier->applyModifiers();

        Styler::classStartAttribute();
        $finalModifier->styler->appendClasses();
        Styler::closeAttribute();

        Styler::startAttribute();
        $finalModifier->styler->appendStyles();
        Styler::closeAttribute();
    }

    public function open(?WebModifier $webModifier = null) : void {
        $this->baseOpen($webModifier);
        echo ' >';
    }

    public function close() : void {
        echo '</div>';
    }

    public function separate(?WebModifier $webModifier = null) : void {
        if($this->sepSize > 0) $this->separateWith("width");
    }

    protected function separateWith($sepType) : void {
        $element = $sepType == "width" ? "span" : "div";
        echo '<' . $element . ' ';
        Styler::startAttribute();
        Styler::addStyle($sepType, $this->sepSize);
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo '</' . $element . '>';
    }

    public function placeItem(Placeable $placeable, WebModifier | null $webModifier = null) : void {
        if($this->itemsCount > 0) $this->separate();
        if($webModifier != null) $placeable->place($webModifier);
        else $placeable->place();
        $this->itemsCount++;
    }

    public function resetItemsCount() : void {
        $this->itemsCount = 0;
    }
}
