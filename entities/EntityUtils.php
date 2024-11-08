<?php

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

    public static function getSimpleValue($item, $key): float | string {
        return ((array) $item)[$key];
    }

    public static function simpleSort(array &$records, string $key, bool $isAsc){
        usort($records, function ($a, $b) use ($key, $isAsc) {
            $aVal = EntityUtils::getSimpleValue($a, $key);
            $bVal = EntityUtils::getSimpleValue($b, $key);
            if($aVal == $bVal) return 0;

            $multiplier = $isAsc ? 1 : -1;
            if($aVal < $bVal) return $multiplier * -1;
            else return $multiplier * 1;
        });
    }
}
