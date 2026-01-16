<?php
namespace Avetify\Themes\Main;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Entities\SetModifier;
use Avetify\Forms\AvtForm;
use Avetify\Forms\Buttons\FormButton;
use Avetify\Interface\CSS;
use Avetify\Interface\WebModifier;
use Avetify\Routing\Routing;
use Avetify\Table\AvtTable;
use Avetify\Themes\Classic\ClassicLabel;

abstract class BaseSetRenderer {
    public AvtForm | null $filtersForm = null;
    public WebModifier $containerModifier;
    public int $marginTop = 20;
    public int $marginBottom = 20;

    public function __construct(public SetModifier $setModifier,
                                public ThemesManager | null $theme,
                                public bool | int $limit = false){
        $this->containerModifier = WebModifier::createInstance();
        $this->postConstruct();
    }

    public function moreRecordFields($record, int $itemIndex){}

    public function renderSet(){
        $this->openCollection();
        foreach ($this->setModifier->currentRecords as $itemIndex => $record){
            if(!$this->isQualified($record)) continue;

            $this->openRecord($record, $itemIndex);
            $this->renderRecordMain($record, $itemIndex);
            $this->moreRecordFields($record, $itemIndex);
            $this->closeRecord($record, $itemIndex);

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
    public function openRecord($record, int $index){}
    public function closeRecord($record, int $index){}
    public function postConstruct(){}

    public function openPage(){
        $this->theme->placeHeader($this->getTitle());
        $this->theme->loadHeaderElements();
    }

    public function closePage(){
        $this->theme->lateImports();
    }

    public function renderBody(){
        if($this->setModifier instanceof AvtTable) {
            $this->setModifier->placeFormDataLists();
        }
        $this->onRecordsAdjusted();

        if($this->setModifier->isSortable){
            $allSortFactors = $this->setModifier->finalSortFactors();
            if(count($allSortFactors) > 0) $this->renderSortLabels();

            $allFilterFields = $this->setModifier->allFilterFields();
            if(count($allFilterFields) > 0){
                $this->initFiltersForm();
                $this->renderFilterFields($this->makeFiltersFormData());
            }

            $allDiscreteFilters = $this->setModifier->allDiscreteFactors();
            if(count($allDiscreteFilters) > 0) $this->renderFilterLabels();
        }

        $this->prepareContainerModifier();
        $this->openContainer();
        $this->renderLeadingItems();
        $this->renderSet();
        $this->closeContainer();
        $this->renderFooter();
    }

    public function renderPage(){
        $this->openPage();
        $this->renderBody();
        $this->closePage();
    }

    public function prepareContainerModifier(){
        $this->containerModifier->pushStyle(CSS::marginTop, $this->marginTop . "px");
        $this->containerModifier->pushStyle(CSS::marginBottom, $this->marginBottom . "px");
    }

    public function onRecordsAdjusted() : void {}

    public abstract function getTitle() : string;
    public abstract function openContainer();
    public abstract function closeContainer();
    public abstract function renderRecordMain($item, int $index);

    public function initFiltersForm() : void {
        $this->filtersForm = new AvtForm($this->getFiltersFormIdentifier());
        $applyButton = new FormButton($this->getFiltersFormIdentifier(), $this->getFiltersApplyButtonID(),
            "Apply");
        $clearButton = new FormButton($this->getFiltersFormIdentifier(), $this->getFiltersApplyButtonID(),
            "Clear", "warning");

        $this->filtersForm->addTrigger($applyButton);
        $this->filtersForm->addTrigger($clearButton);
    }

    public function renderSortLabels(){
        $allSortFactors = $this->setModifier->finalSortFactors();
        echo '<div style="text-align: center; margin-top: 12px;">';

        $this->renderSortLabel("Clear",
            Routing::removeParamFromCurrentLink($this->setModifier->getSortKey()), false);

        $currentSort = $_GET[$this->setModifier->getSortKey()] ?? null;
        $startWithMinus = $currentSort && str_starts_with($currentSort, "-");
        $pureSort = null;
        if($currentSort != null){
            $pureSort = $startWithMinus ? substr($currentSort, 1) : $currentSort;
        }

        foreach ($allSortFactors as $sortFactor){
            $alterStyle = false;
            $finalTitle = $sortFactor->title;
            $nextDescending = $sortFactor->descIsDefault;
            if($currentSort && $pureSort == $sortFactor->factorKey){
                $nextDescending = !$startWithMinus;
                $alterStyle = true;
                $finalTitle .= ($startWithMinus ? " ↓" : " ↑");
            }
            $finalSortFactor = ($nextDescending ? "-" : "") . $sortFactor->factorKey;

            $this->renderSortLabel($finalTitle,
                Routing::addParamToCurrentLink($this->setModifier->getSortKey(), $finalSortFactor), $alterStyle);
        }

        echo '</div>';
    }

    public function renderFilterLabels(){
        $allFilters = $this->setModifier->allDiscreteFactors();

        foreach ($allFilters as $filter){
            if(count($filter->discreteFilters) <= 0) return;

            $filterKey = $filter->getElementIdentifier();
            echo '<div style="text-align: center; margin-top: 12px;">';

            $this->renderFilterLabel("Clear",
                Routing::removeParamFromCurrentLink($filterKey), false);

            $currentFilter = $_GET[$filterKey] ?? null;
            foreach ($filter->discreteFilters as $discreteFilterTitle => $discreteFilterValue){
                $alterStyle = $currentFilter && $currentFilter == $discreteFilterValue;
                $this->renderFilterLabel($discreteFilterTitle,
                    Routing::addParamToCurrentLink($filterKey, $discreteFilterValue), $alterStyle);
            }

            echo '</div>';
        }
    }

    public function makeFiltersFormData() {
        $filterFields = $this->setModifier->allFilterFields();
        $formData = [];

        foreach ($filterFields as $filterField){
            if(!method_exists($filterField->recordField, "getElementIdentifier")) continue;
            $filterKey = $filterField->recordField->getElementIdentifier();
            if(isset($_REQUEST[$filterKey])) {
                $formData[$filterField->key] = $_REQUEST[$filterKey];
            }
        }
        return $formData;
    }

    public function renderFilterFields($filtersFormData) : void {
        $this->filtersForm->openForm();

        $niceDiv = new NiceDiv();
        $filtersModifier = WebModifier::createInstance();
        $filtersModifier->pushStyle("margin-top", "16px");
        $filtersModifier->pushStyle("margin-bottom", "8px");
        $niceDiv->open($filtersModifier);

        $filterFields = $this->setModifier->allFilterFields();
        foreach ($filterFields as $filterField){
            $filterField->recordField->presentValue($filtersFormData);
        }
        $niceDiv->separate();
        $this->filtersForm->placeTriggers(0);

        $niceDiv->close();
        $this->filtersForm->closeForm();
    }

    public function renderSortLabel(string $title, string $link, bool $alterStyle): void {
        $finalBg = !$alterStyle ? "Black" : "Black";
        $finalColor = !$alterStyle ? "Cyan" : "GoldenRod";

        $label = new ClassicLabel($title, $link, $finalBg, $finalColor);
        $label->place();
    }

    public function renderFilterLabel(string $title, string $link, bool $alterStyle): void {
        $finalBg = !$alterStyle ? "Black" : "Black";
        $finalColor = !$alterStyle ? "Cyan" : "GoldenRod";

        $label = new ClassicLabel($title, $link, $finalBg, $finalColor);
        $label->place();
    }

    public function getItemBoxIdentifier($record) : string {
        return $this->setModifier->setKey . "__box__" . $this->setModifier->getItemId($record);
    }

    public function getFiltersFormIdentifier() : string {
        return $this->setModifier->setKey . "_" . "filters_form";
    }

    public function getFormIdentifier() : string {
        return $this->setModifier->setKey . "_" . "table_form";
    }

    public function getFiltersApplyButtonID() : string {
        return $this->setModifier->setKey . '_filters_apply';
    }

    public function getFiltersClearButtonID() : string {
        return $this->setModifier->setKey . '_filters_clear';
    }
}
