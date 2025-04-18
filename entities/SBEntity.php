<?php

abstract class SBEntity extends SetModifier {
    public ?DBConnection $conn = null;
    public $latestFetchedRecord = null;

    public function __construct($dbConnection, string $key = "sbn"){
        parent::__construct($key);
        $this->conn = $dbConnection;
    }

    public function getEntityRecords(): array {
        return $this->getRecords();
    }

    public function insertDataExpression(array $data) : string {
        $fields = $this->dataFields();
        $keyExp = "";
        $valueExp = "";

        $count = 0;
        foreach ($data as $key => $value){
            $field = $this->getNormalField($fields, $key);
            if(!$field) continue;
            $count++;
            $finalValue = $field->numeric ? $value : $this->conn->real_escape_string($value);

            if($keyExp){
                $keyExp .= ", ";
                $valueExp .= ", ";
            }
            else {
                $keyExp = "(";
                $valueExp = "(";
            }

            if(!$field->numeric) $valueExp .= '"';
            $keyExp .= $key;
            $valueExp .= $finalValue;
            if(!$field->numeric) $valueExp .= '"';
        }
        if(!$count) return "";

        foreach ($this->entityWhereFields() as $key => $value) {
            $keyExp .= ", ";
            $keyExp .= $key;
            $valueExp .= ", ";
            $valueExp .= $value;
        }

        $keyExp .= ')';
        $valueExp .= ')';

        return ' ' . $keyExp . ' VALUES ' . $valueExp . ' ';
    }

    public function updateDataExpression($pk, array $data) : string {
        $fields = $this->dataFields();
        $exp = "";

        $count = 0;
        foreach ($data as $key => $value){
            $field = $this->getNormalField($fields, $key);
            if(!$field) continue;
            $finalValue = $field->numeric ? $value : $this->conn->real_escape_string($value);

            $count++;
            if($exp) $exp .= ', ';
            $exp .= $key;
            $exp .= '=';
            if(!$field->numeric) $exp .= '"';
            $exp .= $finalValue;
            if(!$field->numeric) $exp .= '"';
        }
        if(!$count) return "";
        $whereExp = $this->whereExpression($pk);
        $exp .= ($whereExp ? ' WHERE ' . $whereExp : '');

        return ' SET ' . $exp . ' ';
    }

    public function whereExpression($pk = null) : string {
        $fields = $this->entityWhereFields();
        if(count($fields) <= 0 && !$pk) return "";

        $exp = "";
        foreach ($this->entityWhereFields() as $key => $value) {
            if($exp) $exp .= " AND ";
            else $exp .= " ";

            $exp .= $key;
            $exp .= '=';
            $exp .= $value;
        }

        if($pk) {
            if ($exp) $exp .= " AND ";
            $exp .= $this->getSuperKey();
            $exp .= '=';
            if(!$this->isSuperKeyNumeric()) $exp .= "'";
            $exp .= $pk;
            if(!$this->isSuperKeyNumeric()) $exp .= "'";
        }

        return $exp . ' ';
    }

    private function getField($fields, $key, $normalOnly = false) : ?EntityField {
        foreach ($fields as $field){
            if($field->key == $key){
                if(!$normalOnly || !$field->special) return $field;
                return null;
            }
        }
        return null;
    }

    private function getNormalField($fields, $key) : ?EntityField {
        return $this->getField($fields, $key, true);
    }

    public function getRecordQuery($pk) : string {
        return "SELECT * FROM " . $this->getTableName() . ' WHERE ' . $this->whereExpression($pk);
    }

    public function getRecordsQuery($offset = 0, $limit = 100) : string {
        $we = $this->whereExpression();
        return "SELECT * FROM " . $this->getTableName()
            . ($we ? (' WHERE ' . $this->whereExpression()) : "") .
            ' LIMIT ' . $limit . ' OFFSET ' . $offset;
    }

    public function insertRecordQuery(array $data) : string {
        $insExp = $this->insertDataExpression($data);
        if(!$insExp) return "";
        return "INSERT INTO " . $this->getTableName() . $insExp;
    }

    public function updateRecordQuery($pk, array $data) : string {
        $upExp = $this->updateDataExpression($pk, $data);
        if(!$upExp) return "";
        return "UPDATE " . $this->getTableName() . $upExp;
    }

    public function deleteRecordQuery($pk) : string {
        return "DELETE FROM " . $this->getTableName() . ' WHERE ' . $this->whereExpression($pk);
    }

    public function getRecords($offset = 0, $limit = 100) : array {
        return $this->conn->fetchSet($this->getRecordsQuery($offset, $limit));
    }

    public function getRecord($pk) {
        $record = $this->conn->fetchRow($this->getRecordQuery($pk));
        if($record) $this->extendRecord($record);
        return $record;
    }

    public function getCurrentRecordPk() : string | null {
        if(isset($_GET['pk'])) return $_GET['pk'];
        return null;
    }

    public function getCurrentRecord(){
        $pk = $this->getCurrentRecordPk();
        if($pk){
            $this->latestFetchedRecord = $this->getRecord($_GET['pk']);
            return $this->latestFetchedRecord;
        }
        return null;
    }

    public function fastCurrentRecord(){
        $lr = $this->latestFetchedRecord;
        if($lr != null) return $lr;
        return $this->getCurrentRecord();
    }

    public function checkData($data, $createMode) : bool {
        $isOk = true;
        foreach ($this->dataFields() as $field){
            if($field->required){
                if($createMode && empty($data[$field->key])) {
                    echo $field->key . ' Missed!<br>';
                    $isOk = false;
                }
                else if(!$createMode && isset($data[$field->key]) && empty($data[$field->key])) {
                    echo $field->key . ' Can not be empty!<br>';
                    $isOk = false;
                }
            }
        }
        return $isOk;
    }

    public function insertRecord($data){
        $isOk = $this->checkData($data, true);
        if(!$isOk) return null;

        $result = $this->conn->query($this->insertRecordQuery($data));
        if($result && $this->conn->insert_id){
            return $this->getRecord($this->conn->insert_id);
        }
        else if(!$result){
            echo 'SQL: ' . $this->conn->lastQuery . '<br>';
            echo 'ConnError: ' . $this->conn->error . '<br>';
        }
        return null;
    }

    public function updateRecord($pk, $data){
        $isOk = $this->checkData($data, false);
        if(!$isOk) return null;

        //echo $this->updateRecordQuery($pk, $data) . '<br>';

        $this->conn->query($this->updateRecordQuery($pk, $data));
        return $this->getRecord($pk);
    }

    public function printRecords(){
        $records = $this->getRecords();
        printTable($this->dataFields(), $records, ['have_row_number' => true]);
    }

    public function printEntityLinkedName($entity){
        echo '<a href="' . $this->entityPage() . '?pk=' . $entity[$this->getSuperKey()] . '" target="_blank"';
        echo ' style="text-decoration: none; font-weight: bold;" >';
        echo '<span style="color: Black;">';
        echo $entity[$this->getTitleKey()];
        echo '</sapn>';
        echo '</a>';
    }

    public function extendRecord(&$item){}

    public function printForm($options = null){
        $pk = $this->getCurrentRecordPk();

        echo '<div style="height: 12px;"></div>';
        echo '<div style="width: 80%; margin: auto;">
        <form method="post" name="mbform" enctype="multipart/form-data" onsubmit="' . $this->jsValidateForm() . '">
        <fieldset><legend>Insert & Update</legend>';

        $record = $this->getCurrentRecord();
        if(!$record) return;

        echo '<input type="hidden" name="entity_pk" id="entity_pk" value="' . $pk . '" />';
        foreach ($this->dataFields() as $field){
            if($field->writable === True || ($field->writable && !$pk) ){
                $fieldType = $field->type;
                $title = $field->title;
                $key = $field->key;
                $value = isset($record[$key]) ? $record[$key] : null;

                if($field->avatar){
                    if($record){
                        $avatarSrc = $field->path . "/" . $pk . '.' . $field->extension;
                        if(file_exists($avatarSrc)){
                            echo '<img src="' . $avatarSrc . '?' . time() .
                                '" style="height: 250px; width: auto;"<br><br>';
                        }
                    }
                    echo 'Avatar: ';
                    echo '<input type="file" name="' . $key . '" id="' . $key . '" class="empty"
                       style="font-size: 13pt; margin-bottom: 15px;" ><br>';
                }
                else if($fieldType == "boolean"){
                    echo $title . ' ';
                    echo '<input type="checkbox" value="1" name="' . $key . '"'
                        . ($value ? ' checked ' : '') . '/><br><br>';
                }
                else if($fieldType == "text" || $fieldType == "set_text"){
                    if($fieldType == "set_text"){
                        $value = $value ? implode("\n", $value) : "";
                    }

                    echo '<span style="font-weight: 14;">' . $title . '</span><br>';
                    echo '<textarea ';
                    Styler::startAttribute();
                    Styler::addStyle("margin-bottom", "8px");
                    Styler::closeAttribute();
                    echo ' name="' . $key . '" rows="8" cols="150">' . $value . '</textarea><br>';
                }
                else {
                    echo '<input ';
                    HTMLInterface::addAttribute("type", "text");
                    HTMLInterface::addAttribute("name", $key);
                    HTMLInterface::addAttribute("value", $value ? $value : "");
                    HTMLInterface::addAttribute("placeholder", $title);
                    HTMLInterface::addAttribute("class", "empty");
                    Styler::startAttribute();
                    Styler::addStyle("width", "80%");
                    Styler::addStyle("font-size", "14pt");
                    Styler::addStyle("margin-bottom", "12px");
                    Styler::addStyle("margin-bottom", "12px");
                    if($field->rtl) {
                        Styler::addStyle("font-family", "IranSans");
                        Styler::addStyle("direction", "rtl");
                    }
                    Styler::closeAttribute();
                    HTMLInterface::closeSingleTag();
                }
            }
            else if($pk && $field->printable) {
                $key = $field->key;
                $value = isset($record[$key]) ? $record[$key] : null;
                echo $field->title . ': ' . $value . '<br><br>';
            }
        }

        $this->formMainExtension($options);
        echo '</fieldset>';

        $this->formMoreExtension($options);

        echo '<div style="text-align: center; margin-top: 24px;">' .
            '<button type="submit" name="entity_form" value="submit" 
                style="width: 110px; height: 30px;" >Submit</button>';

        foreach ($this->altTriggers() as $triggerKey => $triggerValue){
            echo '<button type="submit" name="entity_form" value="' . $triggerKey .
                '" style="width: 110px; height: 30px; margin-left: 8px;" />' . $triggerValue . '</button>';
        }
        echo '</div>';

        echo '</form>';
    }

    public function handleForm($options = null){
        if(isset($_POST['entity_form'])){
            $data = $_POST;
            $entityPk = isset($_POST['entity_pk']) ? $data['entity_pk'] : null;

            $avatarFields = [];
            foreach ($this->dataFields() as $field){
                if($field->type == 'boolean' && $field->writable && !isset($data[$field->key])){
                    $data[$field->key] = 0;
                }
                else if($field->autoTimeCreate){
                    if(!$entityPk) $data[$field->key] = time();
                }
                else if($field->autoTimeUpdate){
                    $data[$field->key] = time();
                }
                else if($field->avatar){
                    $avatarFields[] = $field;
                }

                if($field->numeric && empty($data[$field->key])) $data[$field->key] = 0;
            }

            if($entityPk){
                $record = $this->getRecord($entityPk);
                if($record) {
                    $this->adjustUpdateData($data, $record, $options);
                    $this->updateRecord($entityPk, $data);
                }
            }
            else {
                $this->adjustCreateData($data, $options);
                $record = $this->insertRecord($data);
                if ($record) {
                    $entityPk = $record[$this->getSuperKey()];
                    echo "Registered Successfully (";
                    $this->printEntityLinkedName($record);
                    echo ')<br>';
                }
            }

            if($entityPk && count($avatarFields) > 0){
                foreach ($avatarFields as $af){
                    $up = false;
                    $targetFilename = $af->path . '/' . $entityPk;
                    if(!empty($_FILES[$af->key]['name'])){
                        $imageDetails = $_FILES[$af->key];
                        $tmpFilename = $imageDetails['tmp_name'];
                        $orgName = $imageDetails['name'];
                        $orgExtension = getFileExtension($orgName);
                        $targetFilename .= ('.' . $orgExtension);
                        move_uploaded_file($tmpFilename, $targetFilename);
                        $up = true;
                    }
                    else if(isset($data[$af->key])){
                        $avatarSrc = $data[$af->key];
                        $orgExtension = getFileExtension($avatarSrc);
                        $targetFilename .= ('.' . $orgExtension);
                        $this->getNetworkFetcher()->downloadFile($avatarSrc, $targetFilename);
                        $up = true;
                    }

                    if($up){
                        $extensionRequired = ($orgExtension && $orgExtension != $af->extension);
                        $convertRequired = $extensionRequired || $af->maxImageSize ||
                            ($af->forcedWidthDimension > 0 && $af->forcedHeightDimension > 0);


                        if($convertRequired) {
                            convertImage($targetFilename,
                                $extensionRequired ? $af->extension : null,
                                $af->maxImageSize, $af->forcedWidthDimension,
                                $af->forcedHeightDimension);
                        }
                    }
                }
            }

            $this->manualHandleForm();
        }
    }

    //extension inside main legend
    public function formMainExtension($options){}

    //extension in new legends
    public function formMoreExtension($options){}

    public function manualHandleForm(){}

    public function entityPage() : string {
        return "";
    }

    public function jsValidateForm() : string {
        return "return true;";
    }

    public function adjustCreateData(&$data, $options = null){}

    public function adjustUpdateData(&$data, $record, $options = null){}

    public function altTriggers() : array {
        return [];
    }

    public function getTitleKey() : string {
        return "name";
    }

    public function getNetworkFetcher() : NetworkFetcher {
        return new NetworkFetcher();
    }

    public function isSuperKeyNumeric() : bool {
        return true;
    }

    public function openEntityPage(){
        $record = $this->getCurrentRecord();
        $title = $record ? $this->getPageTitle($record) : "Add Record";

        $theme = $this->getTheme();
        $theme->placeHeader($title);
    }

    public function renderEntityPage(){
        $this->openEntityPage();
        $this->handleForm();
        $this->printForm();
    }

    public function getTheme() : ThemesManager {
        return new GreenTheme();
    }

    public function getPageTitle($item) : string {
        return $this->getItemName($item);
    }

    abstract public function getSuperKey();
    abstract public function getTableName();

    /**
     * @return EntityField[] An array of MyClass instances
     */
    abstract public function dataFields() : array;

    abstract public function entityWhereFields() : array;
}
