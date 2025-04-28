<?php

class JSDatalist extends JSDataElement {
    public array $flatRecords = [];
    public function __construct(string $dataSetKey, array $records, string $primaryKey, string $labelKey){
        parent::__construct($dataSetKey, $records, $primaryKey, $labelKey);

        foreach ($this->records as $record){
            $this->flatRecords[] = EntityUtils::getSimpleValue($record, $this->labelKey);
        }
    }

    public function getDatalistElementId() : string {
        return $this->dataSetKey;
    }

    public function getRecordsListJSVarName() : string {
        return $this->dataSetKey . "_" . "records";
    }

    public function place(WebModifier $webModifier = null) {
        ?>
        <script>
            const <?php echo $this->getRecordsListJSVarName(); ?> = <?php echo json_encode($this->records) ?>;
        </script>
        <?php

        HTMLInterface::placeDatalist($this->getDatalistElementId(), $this->flatRecords);
    }

    public function getDatalistInfo() : DatalistInfo {
        return new DatalistInfo($this->getDatalistElementId(),
            $this->getRecordsListJSVarName(), $this->primaryKey, $this->labelKey);
    }
}

class DatalistInfo {
    public function __construct(
            public string $datalistId,
            public string $jsRecordsVarName,
            public string $recordPrimaryKey,
            public string $recordLabelKey
    ){}
}
