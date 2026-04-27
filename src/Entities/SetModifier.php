<?php
namespace Avetify\Entities;

use Avetify\DB\DBConnection;
use Avetify\DB\DBFilter;
use Avetify\DB\DBFilterCollection;
use Avetify\DB\DBFilterInterface;
use Avetify\Entities\BasicProperties\EntityManager;
use Avetify\Entities\BasicProperties\Traits\EntityManagerTrait;
use Avetify\Entities\FilterFactors\DiscreteFilterFactor;
use Avetify\Entities\FilterFactors\FilterFactor;
use Avetify\Entities\FilterFactors\FilterField;
use Avetify\Entities\Models\EntityReceivedSort;
use Avetify\Entities\Models\PaginationConfigs;
use Avetify\Entities\Sorters\Sorter;
use Avetify\Entities\Sorters\SortFactor;
use Avetify\Table\Fields\ConstField;
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
    public PaginationConfigs | null $paginationConfigs = null;
    public string | null $className = null;
    public DBConnection | null $conn = null;
    public string $dbTableName = "";
    public bool $dbMode = false;

    public function __construct(public string $setKey){
        $this->paginationConfigs = $this->createPaginationConfigs();
    }

    /** @return Sorter[] An array of MyClass instances */
    public function finalSortFactors() : array {
        return [];
    }

    /** @return FilterFactor[] */
    public function finalFilterFactors() : array {
        return [];
    }

    /** @return DiscreteFilterFactor[] */
    public function allDiscreteFactors() : array {
        $allFactors = $this->finalFilterFactors();
        $discreteFactors = [];
        foreach ($allFactors as $factor){
            if($factor instanceof DiscreteFilterFactor) $discreteFactors[] = $factor;
        }
        return $discreteFactors;
    }

    /** @return FilterField[] */
    public function allFilterFields() : array {
        $allFactors = $this->finalFilterFactors();
        $filterFields = [];
        foreach ($allFactors as $factor){
            if($factor instanceof FilterField) $filterFields[] = $factor;
        }
        return $filterFields;
    }

    public function getSortKey() : string {
        return $this->setKey . "_sort";
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

    private function paginateRecords() : void {
        if(!$this->paginationConfigs) return;
        $curRecordsSize = count($this->currentRecords);
        $this->paginationConfigs->recordsCount = $curRecordsSize;

        $pageSize = $this->paginationConfigs->pageSize;
        $recordsOffset = $this->currentRecordsFirstRowIndex();

        $this->currentRecords = array_splice($this->currentRecords, $recordsOffset, $pageSize);
    }

    public function currentRecordsFirstRowIndex() : int {
        if(!$this->paginationConfigs) return 0;
        $pageSize = $this->paginationConfigs->pageSize;
        $finalPage = $this->paginationConfigs->getCurrentPage();
        return $pageSize * ($finalPage - 1);
    }

    public function simpleSort(string $key, bool $isAsc = true){
        EntityUtils::simpleSort($this->currentRecords, $key, $isAsc);
    }

    private function filterRecords(){
        $this->currentRecords = [];
        foreach ($this->records as $record){
            $isQualified = true;

            foreach ($this->finalFilterFactors() as $filterFactor){
                $filterKey = null;
                if(method_exists($filterFactor, "getElementIdentifier")){
                    $filterKey = $filterFactor->getElementIdentifier();
                }

                if($filterKey && isset($_REQUEST[$filterKey])){
                    $filterValue = $_REQUEST[$filterKey];
                    if(!$filterFactor->isQualified($record, $filterValue)){
                        $isQualified = false;
                        break;
                    }
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
        if($this->dbMode){
            $this->currentRecords = $this->records;
            return;
        }
        $this->filterRecords();
        $this->sortRecords();
        $this->paginateRecords();
    }

    public function dbUpdateRecords (){
        $this->loadRawRecords($this->fetchDBRecords());
    }

    public function fetchDBRecords() : array {
        $filter = $this->createDBFilter();
        $fetchOrder = $this->createDBFetchOrder();

        $limit = 0;
        $offset = 0;
        if($this->paginationConfigs){
            $this->paginationConfigs->recordsCount = $this->conn->fetchTableSize($this->dbTableName, $filter);
            $limit = $this->paginationConfigs->pageSize;
            $offset = $this->currentRecordsFirstRowIndex();
        }

        if($this->className){
            return $this->conn->fetchTable($this->className, $this->dbTableName, $filter, $fetchOrder, $limit, $offset);
        }
        return $this->conn->fetchTableSet($this->dbTableName, $filter, $fetchOrder, $limit, $offset);
    }

    public function createDBFilter() : DBFilterInterface | null {
        $filter = new DBFilterCollection();
        $constFields = $this->getConstFields();

        if(count($constFields) > 0){
            foreach ($constFields as $constField){
                $filter->addFilter(new DBFilter($constField->key, "=", $constField->value, $constField->isNumeric));
            }
        }

        if($this->isSortable && $this->autoSort){
            $sortFactor = $this->getSortFactor();
            if($sortFactor instanceof SortFactor && $sortFactor->skipEmpties){
                $targetValue = $sortFactor->isNumeric ? 0 : "";
                $filter->addFilter(new DBFilter($sortFactor->factorKey, "<>", $targetValue, $sortFactor->isNumeric));
            }
        }

        foreach ($this->finalFilterFactors() as $filterFactor){
            if(!($filterFactor instanceof SortFactor))

            $filterKey = null;
            if(method_exists($filterFactor, "getElementIdentifier")){
                $filterKey = $filterFactor->getElementIdentifier();
            }

            if($filterKey && isset($_REQUEST[$filterKey])){
                $filterValue = $_REQUEST[$filterKey];
                $filter->addFilter(new DBFilter($filterKey, "=", $filterValue, $filterFactor->isNumeric));
            }
        }

        return $filter;
    }

    public function createDBFetchOrder() : string {
        if(!$this->isSortable || !$this->autoSort) return "";
        $sortFactor = $this->getSortFactor();
        if($sortFactor == null) return "";

        if($sortFactor instanceof SortFactor){
            $dir = $sortFactor->isDescending() ? "DESC" : "ASC";
            $factorKey = $sortFactor->factorKey;
            $dbOrder = "$factorKey $dir";

            if(count($sortFactor->tieBreaks) > 0){
                foreach ($sortFactor->tieBreaks as $tieBreak){
                    [$tbFactor, $skipEmpties, $tbDesc, $tbNumeric] = $sortFactor->_extractDetailsFromTiebreak($tieBreak);
                    $tbDir = $tbDesc ? "DESC" : "ASC";
                    $dbOrder .= ", $tbFactor $tbDir";
                }
            }

            return $dbOrder;
        }

        return "";
    }

    /** @return ConstField[] */
    public function getConstFields() : array {
        return [];
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

    public function getPageRecordsCount() : int {
        return 100;
    }

    /** @return PaginationConfigs|null */
    public function createPaginationConfigs(): ?PaginationConfigs {
        return null;
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
