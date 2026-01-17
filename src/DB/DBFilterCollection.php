<?php
namespace Avetify\DB;

class DBFilterCollection implements DBFilterInterface {
    /** @var DBFilterInterface[] $filters */
    public array $filters = [];

    const AND_MODE = 1;
    const OR_MODE = 2;

    public function __construct(public int $filterMode = self::AND_MODE){}

    /** @param DBFilterInterface[] $initFilters */
    public static function create(array $initFilters, int $initFilterMode = self::AND_MODE) : DBFilterCollection {
        $newFilterCollection = new DBFilterCollection($initFilterMode);
        $newFilterCollection->filters = $initFilters;
        return $newFilterCollection;
    }

    public function addFilter(DBFilter $filter){
        $this->filters[] = $filter;
    }

    public function addFilterCollection(DBFilterCollection $filterCollection){
        $this->filters[] = $filterCollection;
    }

    public function toRawQuery() : string {
        if(count($this->filters) <= 0) return "";

        $query = "";
        foreach ($this->filters as $filter){
            if($query) $query .= (" " . ($this->filterMode == self::AND_MODE ? "AND" : "OR") . " ");
            $query .= $filter->toRawQuery();
        }

        if(count($this->filters) > 1) $query = "($query)";
        return $query;
    }

    public function count() : int {
        return count($this->filters);
    }
}
