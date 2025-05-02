<?php

abstract class ModernRatioGallery extends SetRenderer implements EntityImageRatio {
    public RecordContextMenu | null $contextMenu = null;
    private float $curRowOffset = 0;

    public function __construct(SetModifier $setModifier, ModernTheme $theme, string $title,
                                public int $unitSize, public int $maxRowUnits){
        parent::__construct($setModifier, $theme, $title);
    }

    public function openRowDiv(){
        $niceDiv = new NiceDiv(0);
        $niceDiv->addStyle("margin-top", "8px");
        $niceDiv->addStyle("margin-bottom", "8px");
        $niceDiv->addStyle("gap", "8px");
        $niceDiv->open();
        $this->curRowOffset = 0;
    }

    public function renderSet() {
        if(count($this->setModifier->currentRecords) > 0){
            $this->openRowDiv();
        }
        parent::renderSet();
        if(count($this->setModifier->currentRecords) > 0){
            HTMLInterface::closeDiv();
        }
    }

    public function renderRecordMain($item, int $index) {
        $itemWidthUnits = $this->getItemRatio($item);
        if(($this->curRowOffset + $itemWidthUnits) > $this->maxRowUnits){
            HTMLInterface::closeDiv();
            $this->openRowDiv();
        }

        $link = $this->getItemLink($item);
        if($link){
            echo '<a ';
            HTMLInterface::addAttribute("href", $link);
            HTMLInterface::closeTag();
        }

        echo '<img ';
        HTMLInterface::addAttribute("src", $this->setModifier->getItemImage($item));
        Styler::startAttribute();
        Styler::addStyle("height", $this->unitSize . "px");
        Styler::addStyle("width", "auto");
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        if($link){
            echo '</a>';
        }

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

    public function getItemLink($record): string {
        return "";
    }
}