<?php

abstract class SetRenderer {
    public function __construct(public SetModifier $setModifier, public ThemesManager | null $theme){}

    public function placeHeader(){
        $this->theme->placeHeader($this->getTitle());
    }

    public function renderRecords(){
        $itemIndex = 0;
        foreach ($this->setModifier->currentRecords as $index => $record){
            if(!$this->isQualified($record)) continue;
            $this->renderRecord($record, $itemIndex++);
        }
    }

    public function isQualified($item) : bool {
        return true;
    }

    public function renderLeadingItems(){}

    public function renderFooter(){}

    public function renderPage(){
        $this->placeHeader();
        $this->setModifier->adjustRecords();
        $this->onRecordsAdjusted();

        $allSortFactors = $this->setModifier->finalSortFactors();
        if(count($allSortFactors) > 0) $this->setModifier->renderSortLabels();


        $this->openContainer();
        $this->renderLeadingItems();
        $this->renderRecords();
        $this->closeContainer();
        $this->renderFooter();
    }

    public function onRecordsAdjusted() : void {}

    public abstract function getTitle() : string;
    public abstract function openContainer();
    public abstract function closeContainer();
    public abstract function renderRecord($item, $index);

    public function getItemBoxIdentifier($record) : string {
        return $this->setModifier->setKey . "__box__" . $this->setModifier->getItemId($record);
    }
}
