<?php

class JSDatalist extends JSDataElement {
    public array $flatRecords = [];
    public function __construct(string $dataSetKey, array $records,
                                string $primaryKey = "", string $labelKey = ""){
        parent::__construct($dataSetKey, $records, $primaryKey, $labelKey);

        foreach ($this->records as $record){
            $this->flatRecords[] = $this->getItemTitle($record);
        }
    }

    public function getDatalistElementId() : string {
        return $this->dataSetKey;
    }

    public function getRecordsListJSVarName() : string {
        return $this->dataSetKey . "_" . "records";
    }

    public function getRecordsIdsMapJSVarName() : string {
        return $this->dataSetKey . "_" . "map_id";
    }

    public function getRecordsNamesMapJSVarName() : string {
        return $this->dataSetKey . "_" . "map_name";
    }

    public function place(WebModifier $webModifier = null) {
        $plcRecords = [];
        $plcNamesMap = [];
        $plcIdsMap = [];

        foreach ($this->records as $record){
            $plcRecord = (array)($record);
            $plcRecord['main_jsdl_avatar'] = $this->getItemImage($record);
            $plcRecord['main_jsdl_name'] = $this->getItemTitle($record);
            $plcRecord['main_jsdl_id'] = $this->getItemId($record);

            $plcNamesMap[strtolower($plcRecord['main_jsdl_name'])] = count($plcRecords);
            $plcIdsMap[strtolower($plcRecord['main_jsdl_id'])] = count($plcRecords);
            $plcRecords[] = $plcRecord;
        }

        ?>
        <script>
            const <?php echo $this->getRecordsListJSVarName(); ?> = <?php echo json_encode($plcRecords) ?>;
            const <?php echo $this->getRecordsIdsMapJSVarName(); ?> = <?php echo json_encode($plcIdsMap) ?>;
            const <?php echo $this->getRecordsNamesMapJSVarName(); ?> = <?php echo json_encode($plcNamesMap) ?>;
        </script>
        <?php

        HTMLInterface::placeDatalist($this->getDatalistElementId(), $this->flatRecords);
    }
}
