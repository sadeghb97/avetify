<?php

abstract class BaseSetRenderer {
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
        $this->theme->placeHeader($this->getTitle());
        $this->theme->loadHeaderElements();
    }

    public function renderBody(){
        $this->onRecordsAdjusted();

        $allSortFactors = $this->setModifier->finalSortFactors();
        if($this->setModifier->isSortable && count($allSortFactors) > 0){
            $this->renderSortLabels();
        }

        $this->openContainer();
        $this->renderLeadingItems();
        $this->renderSet();
        $this->closeContainer();
        $this->renderFooter();
    }

    public function renderPage(){
        $this->openPage();
        $this->renderBody();
    }

    public function onRecordsAdjusted() : void {}

    public abstract function getTitle() : string;
    public abstract function openContainer();
    public abstract function closeContainer();
    public abstract function renderRecordMain($item, int $index);

    public function renderSortLabels(){
        $allSortFactors = $this->setModifier->finalSortFactors();
        echo '<div style="text-align: center; margin-top: 12px;">';
        $defaultBg = 'Black';
        $defaultColor = 'Cyan';
        $alterBg = 'Black';
        $alterColor = 'GoldenRod';
        printLabel("Clear", Routing::removeParamFromCurrentLink($this->setModifier->getSortKey()),
            $defaultBg, $defaultColor);

        $currentSort = isset($_GET[$this->setModifier->getSortKey()]) ?
            $_GET[$this->setModifier->getSortKey()] : null;
        $startWithMinus = $currentSort ? str_starts_with($currentSort, "-") : false;
        $pureSort = null;
        if($currentSort != null){
            $pureSort = $startWithMinus ? substr($currentSort, 1) : $currentSort;
        }

        foreach ($allSortFactors as $sortFactor){
            $finalBg = $defaultBg;
            $finalColor = $defaultColor;
            $finalTitle = $sortFactor->title;
            $nextDescending = $sortFactor->descIsDefault;
            if($currentSort && $pureSort == $sortFactor->factorKey){
                $nextDescending = !$startWithMinus;
                $finalBg = $alterBg;
                $finalColor = $alterColor;
                $finalTitle .= ($startWithMinus ? " ↓" : " ↑");
            }
            $finalSortFactor = ($nextDescending ? "-" : "") . $sortFactor->factorKey;

            printLabel($finalTitle, Routing::addParamToCurrentLink($this->setModifier->getSortKey(),
                $finalSortFactor), $finalBg, $finalColor);
        }

        echo '</div>';
    }

    public function getItemBoxIdentifier($record) : string {
        return $this->setModifier->setKey . "__box__" . $this->setModifier->getItemId($record);
    }
}
