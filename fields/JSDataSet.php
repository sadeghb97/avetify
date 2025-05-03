<?php

class JSDataSet extends JSDataElement {
    public function place(WebModifier $webModifier = null){
        echo '<template ';
        HTMLInterface::addAttribute("id", $this->dataSetKey);
        HTMLInterface::closeTag();

        foreach ($this->records as $record){
            $key = $this->getItemId($record);
            $label = $this->getItemTitle($record);

            echo '<option ';
            HTMLInterface::addAttribute("value", $key);
            HTMLInterface::closeTag();
            echo $label;
            echo '</option>';
        }

        echo '</template>';
    }
}
