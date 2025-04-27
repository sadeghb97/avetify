<?php

class JSDataSet implements Placeable {
    public function __construct(public string $dataSetKey, public array $records,
                                public string $primaryKey, public string $labelKey){
    }

    public function place(WebModifier $webModifier = null){
        echo '<template ';
        HTMLInterface::addAttribute("id", $this->dataSetKey);
        HTMLInterface::closeTag();

        foreach ($this->records as $record){
            $key = EntityUtils::getSimpleValue($record, $this->primaryKey);
            $label = EntityUtils::getSimpleValue($record, $this->labelKey);

            echo '<option ';
            HTMLInterface::addAttribute("value", $key);
            HTMLInterface::closeTag();
            echo $label;
            echo '</option>';
        }

        echo '</template>';
    }
}
