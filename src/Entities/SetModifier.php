<?php
namespace Avetify\Entities;

use Avetify\Themes\Main\SetRenderer;
use function Avetify\Utils\minimize;

abstract class SetModifier implements EntityManager {
    use EntityManagerTrait;

    public array $records = [];
    public array $currentRecords = [];
    public bool $isSortable = true;
    public bool $isEditable = true;
    public SetRenderer | null $renderer = null;
    public int $maxTitleLength = 0;

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

    public function getDefaultFactor() : Sorter | null {
        return null;
    }

    public function getSortFactor() : Sorter | null {
        if(!isset($_GET[$this->getSortKey()])) return $this->getDefaultFactor();
        $sortFactorKey = $_GET[$this->getSortKey()];
        $startWithMinus = str_starts_with($sortFactorKey, "-");
        $pureSortFactorKey = $startWithMinus ? substr($sortFactorKey, 1) : $sortFactorKey;
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
        return minimize($this->getItemTitle($item), $this->maxTitleLength);
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
