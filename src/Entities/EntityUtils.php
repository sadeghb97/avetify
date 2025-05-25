<?php
namespace Avetify\Entities;

use Avetify\DB\DBConnection;
use Avetify\Entities\Sorters\SortDetails;

class EntityUtils {

    //data: associative ba klide id
    public static function updateEntityRecords(
        DBConnection $conn,
        string $tableName,
        string $idKey,
        array $data,
        array $oldData
    ) : bool {
        $isChanged = false;
        foreach ($data as $entityId => $entityData){
            if(count($entityData) == 0) continue;
            $sql = "UPDATE $tableName SET ";

            $firstKey = true;
            $isRequired = false;
            foreach ($entityData as $dataKey => $dataValue){
                if($dataKey == $idKey) continue;

                if(!$isRequired){
                    $isRequired = !isset($oldData[$entityId]) ||
                        !isset($oldData[$entityId][$dataKey]) ||
                        $oldData[$entityId][$dataKey] != $dataValue;
                }

                $adjustedValue = $conn->real_escape_string($dataValue);
                $isString = is_string($dataValue);
                $dataWrapper = $isString ? "'" : "";
                if(!$firstKey) $sql .= ', ';
                $firstKey = false;
                $sql .= (' ' . $dataKey . '=' . $dataWrapper . $adjustedValue . $dataWrapper . ' ');
            }

            $sql .= (" WHERE $idKey='" . $entityId . "'");

            if($isRequired) {
                if (!$conn->query($sql)) {
                    echo "ConnError: " . $sql . ' -> ' . $conn->error;
                }
                else $isChanged = true;
            }
        }
        return $isChanged;
    }

    public static function defaultValueGetter($key) : ValueGetter {
        return new class ($key) implements ValueGetter {
            public function __construct(public string $key){
            }

            public function getValue($item): string | float {
                return ((array) $item)[$this->key];
            }
        };
    }

    public static function getSimpleValue($item, $keys): float | string | null {
        $finalKeys = is_array($keys) ? $keys : [$keys];
        $ar = ((array) $item);

        foreach ($finalKeys as $finalKey){
            if(!isset($ar[$finalKey])) return "";
            else $ar = $ar[$finalKey];

            if(is_object($ar)) $ar = ((array) $ar);
        }

        return $ar;
    }

    public static function getMultiChoiceValue($item, array $keysList) : float | string | null {
        foreach ($keysList as $key){
            $res = self::getSimpleValue($item, $key);
            if($res !== "" && $res !== null) return $res;
        }
        return "";
    }

    public static function simpleSort(array &$records, string $key, bool $isAsc){
        self::multiSort($records, [new SortDetails($key, $isAsc)]);
    }


    /**
     * @param array $records
     * @param SortDetails[] $sortDetails */
    public static function multiSort(array &$records, array $sortDetails){
        usort($records, function ($a, $b) use ($sortDetails) {
            $sdi = 0;
            do {
                $currentSortDetails = $sortDetails[$sdi];
                $aVal = EntityUtils::getSimpleValue($a, $currentSortDetails->sortKey);
                $bVal = EntityUtils::getSimpleValue($b, $currentSortDetails->sortKey);
                $sdi++;
            } while($aVal == $bVal && $sdi < count($sortDetails));

            if($aVal == $bVal) return 0;

            $multiplier = $currentSortDetails->isAsc ? 1 : -1;
            if($aVal < $bVal) return $multiplier * -1;
            else return $multiplier * 1;
        });
    }
}
