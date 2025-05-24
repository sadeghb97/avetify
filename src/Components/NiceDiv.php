<?php
namespace Avetify\Components;

use Avetify\Interface\AvtContainer;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class NiceDiv implements AvtContainer {
    private int $itemsCount = 0;

    public array $styles = [
        "display" => "flex",
        "align-items" => "center",
        "justify-content" => "center",
        "flex-wrap" => "wrap",
        "gap" => "4px"
    ];

    public static function justOpen(){
        $niceDiv = new NiceDiv(0);
        $niceDiv->open();
    }

    public array $htmlModifiers = [];

    public function __construct(public string $sepSize = "0px"){}

    public function addStyle($key, $value){
        $this->styles[$key] = $value;
    }
    public function addModifier($key, $value){
        $this->htmlModifiers[$key] = $value;
    }

    public function baseOpen(WebModifier $webModifier = null){
        echo '<div ';
        Styler::startAttribute();
        foreach ($this->styles as $key => $value) Styler::addStyle($key, $value);
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        foreach ($this->htmlModifiers as $modifierKey => $modifierValue){
            HTMLInterface::addAttribute($modifierKey, $modifierValue);
        }
        HTMLInterface::applyModifiers($webModifier);
    }

    public function open(WebModifier $webModifier = null){
        $this->baseOpen($webModifier);
        echo ' >';
    }

    public function close(){
        echo '</div>';
    }

    public function separate(WebModifier $webModifier = null){
        if($this->sepSize > 0) $this->separateWith("width");
    }

    protected function separateWith($sepType){
        $element = $sepType == "width" ? "span" : "div";
        echo '<' . $element . ' ';
        Styler::startAttribute();
        Styler::addStyle($sepType, $this->sepSize);
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo '</' . $element . '>';
    }

    public function placeItem(Placeable $placeable, WebModifier | null $webModifier = null){
        if($this->itemsCount > 0) $this->separate();
        if($webModifier != null) $placeable->place($webModifier);
        else $placeable->place();
        $this->itemsCount++;
    }

    public function resetItemsCount(){
        $this->itemsCount = 0;
    }
}
