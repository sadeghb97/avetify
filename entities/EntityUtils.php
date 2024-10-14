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
}
