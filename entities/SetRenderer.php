<?php

abstract class SetRenderer {
    public function __construct(public SetModifier $setModifier,
                                public ThemesManager | null $theme,
                                public bool | int $limit = false){}

    public function moreRecordFields($record, int $itemIndex){}

    public function renderSet(){
        $this->openCollection();
        foreach ($this->setModifier->currentRecords as $itemIndex => $record){
            if(!$this->isQualified($record)) continue;

            $this->openRecord($record);
            $this->renderRecordMain($record, $itemIndex);
            $this->moreRecordFields($record, $itemIndex);
            $this->closeRecord($record);

            if($this->limit && ($itemIndex + 1) >= $this->limit) break;
        }
        $this->closeCollection();
    }

    public function isQualified($item) : bool {
        return true;
    }

    public function renderLeadingItems(){}

    public function renderFooter(){}

    public function openCollection(WebModifier $webModifier = null){}
    public function closeCollection(WebModifier $webModifier = null){}
    public function openRecord($record){}
    public function closeRecord($record){}

    public function openPage(){
        $theme = $this->getTheme();
        $theme->placeHeader($this->getTitle());
        $theme->loadHeaderElements();
    }

    public function renderPage(){
        $this->openPage();
        $this->setModifier->adjustRecords();
        $this->onRecordsAdjusted();

        $allSortFactors = $this->setModifier->finalSortFactors();
        if(count($allSortFactors) > 0) $this->renderSortLabels();

        $this->openContainer();
        $this->renderLeadingItems();
        $this->renderSet();
        $this->closeContainer();
        $this->renderFooter();
    }

    public function onRecordsAdjusted() : void {}

    public abstract function getTitle() : string;
    public abstract function openContainer();
    public abstract function closeContainer();
    public abstract function renderRecordMain($item, int $index);

    public function renderSortLabels(){}

    public function getItemBoxIdentifier($record) : string {
        return $this->setModifier->setKey . "__box__" . $this->setModifier->getItemId($record);
    }
}
