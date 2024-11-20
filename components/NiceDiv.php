<?php

class NiceDiv {
    private int $itemsCount = 0;

    public array $styles = [
        "display" => "flex",
        "align-items" => "center",
        "justify-content" => "center",
        "flex-wrap" => "wrap",
        "gap" => "4px"
    ];

    public function __construct(public string $sepSize){}

    public function addStyle($key, $value){
        $this->styles[$key] = $value;
    }

    public function open(){
        echo '<div ';
        Styler::startAttribute();
        foreach ($this->styles as $key => $value)
        Styler::addStyle($key, $value);
        Styler::closeAttribute();
        echo ' >';
    }

    public function close(){
        echo '</div>';
    }

    public function separate(){
        $this->separateWith("width");
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

    public function placeItem(Placeable $placeable){
        if($this->itemsCount > 0) $this->separate();
        $placeable->place();
        $this->itemsCount++;
    }

    public function resetItemsCount(){
        $this->itemsCount = 0;
    }
}
