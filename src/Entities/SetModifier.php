<?php
namespace Avetify\Entities;

use Avetify\Entities\BasicProperties\EntityManager;
use Avetify\Entities\BasicProperties\Traits\EntityManagerTrait;
use Avetify\Entities\FilterFactors\FilterFactor;
use Avetify\Entities\Models\EntityReceivedSort;
use Avetify\Entities\Sorters\Sorter;
use Avetify\Entities\Sorters\SortFactor;
use Avetify\Themes\Main\SetRenderer;
use Avetify\Utils\StringUtils;

abstract class SetModifier implements EntityManager {
    use EntityManagerTrait;

    public array $records = [];
    public array $currentRecords = [];
    public bool $isSortable = true;
    public bool $isEditable = true;
    public SetRenderer | null $renderer = null;
    public int $maxTitleLength = 0;
    public bool $autoSort = true;

    public function __construct(public string $setKey){}

    /** @return Sorter[] An array of MyClass instances */
    public function finalSortFactors() : array {
        return [];
    }

    /** @return FilterFactor[] An array of MyClass instances */
    public function finalFilterFactors() : array {
        return [];
    }

    public function getSortKey() : string {
        return $this->setKey . "_sort";
    }

    public function getFilterKey($factorKey) : string {
        return $this->setKey . "_" . $factorKey;
    }

    public function getSortRawToken() : ?string {
        return $_GET[$this->getSortKey()] ?? null;
    }

    public function getParsedReceivedSort() : ?EntityReceivedSort {
        $rawSortToken = $this->getSortRawToken();
        if($rawSortToken == null) return null;

        $startWithMinus = str_starts_with($rawSortToken, "-");
        $pureSortFactorKey = $startWithMinus ? substr($rawSortToken, 1) : $rawSortToken;

        return new EntityReceivedSort($pureSortFactorKey, $startWithMinus);
    }

    public function getDefaultFactor() : Sorter | null {
        $allSortFactors = $this->finalSortFactors();
        foreach ($allSortFactors as $sf){
            if($sf instanceof SortFactor && $sf->isDefaultSort) return $sf;
        }
        return null;
    }

    public function getSortFactor() : Sorter | null {
        $receivedSort = $this->getParsedReceivedSort();
        if(!$receivedSort) return $this->getDefaultFactor();
        $startWithMinus = $receivedSort->alterDirection;
        $pureSortFactorKey = $receivedSort->key;
        $allSortFactors = $this->finalSortFactors();

        foreach ($allSortFactors as $sf){
            if($sf instanceof SortFactor && $sf->factorKey == $pureSortFactorKey){
                $nextIsDescending = $startWithMinus;
                if($sf->isDescending() != $nextIsDescending) $sf->toggleDirection();
                return $sf;
            }
        }

        return $this->getDefaultFactor();
    }

    private function sortRecords(){
        if(!$this->isSortable || !$this->autoSort) return;
        $sortFactor = $this->getSortFactor();
        if($sortFactor == null) return;
        usort($this->currentRecords, [$sortFactor, 'compare']);
    }

    public function simpleSort(string $key, bool $isAsc = true){
        EntityUtils::simpleSort($this->currentRecords, $key, $isAsc);
    }

    private function filterRecords(){
        $this->currentRecords = [];
        foreach ($this->records as $record){
            $isQualified = true;

            foreach ($this->finalFilterFactors() as $filterFactor){
                $filterKey = $this->getFilterKey($filterFactor->key);
                if(isset($_GET[$filterKey]) && !$filterFactor->isQualified($record, $_GET[$filterKey])){
                    $isQualified = false;
                    break;
                }
            }

            if($isQualified) $this->currentRecords[] = $record;
        }
    }

    public function loadRawRecords($rawRecords){
        $this->records = $rawRecords;
        $this->adjustRecords();
    }

    public function adjustRecords(){
        $this->filterRecords();
        $this->sortRecords();
    }

    public function getCurrentRecordsIndexes() : array {
        $map = [];
        foreach ($this->currentRecords as $index => $record){
            $id = $this->getItemId($record);
            $map[$id] = $index;
        }
        return $map;
    }

    public function getAdjustedTitle($item) : string {
        return StringUtils::minimize($this->getItemTitle($item), $this->maxTitleLength);
    }

    public function getAllAvailableItemsCount() : int {
        return count($this->records);
    }

    public function getCurrentRecordCount() : int {
        return count($this->currentRecords);
    }

    public function openPage(string $title = ""){
        if($title) $this->renderer->title = $title;
        $this->renderer->openPage();
    }

    public function renderBody(){
        $this->renderer->renderBody();
    }

    public function renderPage(string $title = ""){
        if($title) $this->renderer->title = $title;
        $this->renderer->renderPage();
    }
}
