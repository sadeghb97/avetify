<?php

abstract class ModernRatioGallery extends SetRenderer implements EntityImage, EntityImageRatio {
    public RecordContextMenu | null $contextMenu = null;
    private float $curRowOffset = 0;

    public function __construct(SetModifier $setModifier, public int $unitSize, public int $maxRowUnits,
                                public string $title){
        parent::__construct($setModifier, new ModernTheme());
    }

    public function openRowDiv(){
        $niceDiv = new NiceDiv(0);
        $niceDiv->addStyle("margin-top", "8px");
        $niceDiv->addStyle("margin-bottom", "8px");
        $niceDiv->addStyle("gap", "8px");
        $niceDiv->open();
        $this->curRowOffset = 0;
    }

    public function renderRecords() {
        if(count($this->setModifier->currentRecords) > 0){
            $this->openRowDiv();
        }
        parent::renderRecords();
        if(count($this->setModifier->currentRecords) > 0){
            HTMLInterface::closeDiv();
        }
    }

    public function renderRecord($item, $index) {
        $itemWidthUnits = $this->getItemRatio($item);
        if(($this->curRowOffset + $itemWidthUnits) > $this->maxRowUnits){
            HTMLInterface::closeDiv();
            $this->openRowDiv();
        }

        echo '<img ';
        HTMLInterface::addAttribute("src", $this->getItemImage($item));
        Styler::startAttribute();
        Styler::addStyle("height", $this->unitSize . "px");
        Styler::addStyle("width", "auto");
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        $this->curRowOffset += $itemWidthUnits;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function openContainer(){
        $div = new VertDiv(0);
        $div->addStyle("width", "92%");
        $div->addStyle("margin", "auto");
        $div->open();
    }

    public function closeContainer(){
        HTMLInterface::closeDiv();
    }
}