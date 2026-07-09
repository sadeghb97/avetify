<?php
namespace Avetify\Searchers\Utils;

use ReflectionMethod;

class ListerLabelUtils {
    public static function listerDeclaresSecondarySort(object $lister): bool {
        if(!method_exists($lister, "getSecondarySortFactor")) return false;
        $decl = (new ReflectionMethod($lister, "getSecondarySortFactor"))->getDeclaringClass()->getName();
        return $decl === get_class($lister);
    }

    public static function listerItemLabel(object $lister, object $item): string {
        $listIndex = $lister->catOrgPkToListIndex($item);
        $titles = $lister->getListTitles();
        $label = $titles[$listIndex] ?? "";
        if(self::listerDeclaresSecondarySort($lister)){
            $factor = $lister->getSecondarySortFactor($item);
            if($factor !== null && $factor !== ""){
                $label .= " #" . ((int)$factor + 1);
            }
        }
        return $label;
    }

    /** @param object[] $listers */
    public static function labelsForItem(array $listers, object $item): array {
        $labels = [];
        foreach ($listers as $lister){
            $label = self::listerItemLabel($lister, $item);
            if($label !== "") $labels[] = $label;
        }
        return $labels;
    }
}
