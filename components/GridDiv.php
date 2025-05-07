<?php

class GridDiv implements AVTContainer {
    private int $counter = 0;
    protected VertDiv $mainDiv;
    protected NiceDiv $innerDiv;
    protected ?NiceDiv $itemDiv = null;

    public function __construct(public int $cols, public int $rowInnerGape = 6,
                                public int $colInnerGape = 6){
        $this->mainDiv = new VertDiv($this->colInnerGape);
        $this->innerDiv = new NiceDiv($this->rowInnerGape);
    }

    public function setItemFixedSize(int $width, int $height = 0){
        $this->itemDiv = new NiceDiv(0);
        if($width > 0){
            $this->itemDiv->addStyle("width", $width);
        }
        if($height > 0){
            $this->itemDiv->addStyle("height", $height);
        }
    }

    public function open(WebModifier $webModifier = null) {
        $this->mainDiv->open();
        $this->innerDiv->open();
        if($this->itemDiv) $this->itemDiv->open();
    }

    public function separate(WebModifier $webModifier = null){
        $this->counter++;
        if(($this->counter % $this->cols) == 0){
            if($this->itemDiv) $this->itemDiv->close();
            $this->innerDiv->close();
            $this->mainDiv->separate();
            $this->innerDiv->open();
            if($this->itemDiv) $this->itemDiv->open();
        }
        else {
            if($this->itemDiv){
                $this->itemDiv->close();
                $this->itemDiv->open();
            }
            $this->innerDiv->separate();
        }
    }

    public function close(WebModifier $webModifier = null){
        if($this->itemDiv) HTMLInterface::closeDiv();
        HTMLInterface::closeDiv();
        HTMLInterface::closeDiv();
    }
}
