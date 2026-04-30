<?php
namespace Avetify\Table\Fields\EditableFields\SelectFields;

use Avetify\DB\Filters\DBFilterCollection;
use Avetify\DB\Filters\DBFilterInterface;
use Avetify\DB\Filters\RawSetFilter;
use Avetify\Table\Fields\EditableFields\EditableField;

class BaseSetSelectField extends EditableField {
    public function isQualified($item, $param): bool {
        $setValue = $this->getValue($item);
        if(!$setValue) return false;

        $filterValue = $param;
        if(!$filterValue) return true;

        $existsList = explode(",", $setValue);
        $filterList = explode(",", $filterValue);

        $existsSet = [];
        foreach ($existsList as $i) $existsSet[$i] = true;

        foreach ($filterList as $filterItem){
            if(empty($existsSet[$filterItem])) return false;
        }
        return true;
    }

    public function dbQualifyingFilter($param): DBFilterInterface | null {
        if(!$param) return null;
        $targetList = explode(",", $param);
        $filterCollection = new DBFilterCollection();
        foreach ($targetList as $target){
            $filterCollection->addFilter(
                new RawSetFilter($this->key, $target, $this->isNumeric)
            );
        }
        return $filterCollection;
    }
}
