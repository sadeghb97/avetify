<?php
namespace Avetify\Entities\Sorters;

class SortDetails {
    public function __construct(public string $sortKey, public bool $isAsc){
    }
}
