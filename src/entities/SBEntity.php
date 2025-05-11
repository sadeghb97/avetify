<?php

abstract class SBEntity extends SetModifier {
    public ?DBConnection $conn = null;
    public $latestFetchedRecord = null;
    public bool $redirectOnInsert = true;
    public bool $deletable = true;
    public string $entityName = "Record";
    public string $entityModel = "";
    public string $urlParamEntityKey = "pk";

    public function __construct($dbConnection, string $key = "sbn"){
        parent::__construct($key);
        $this->conn = $dbConnection;
    }

    public function getEntityRecords(): array {
        return $this->getRecords();
    }

    /** @return SBEntityItem[] */
    public function getRecordObjects(int $offset, int $limit) : array {
        $recs = $this->getRecords($offset, $limit);
        $dsRecords = [];
        foreach ($recs as $rec){
            $dsRecords[] = $this->createEntityItem($rec);
        }
        return $dsRecords;
    }

    /** @return SBEntityItem[] */
    public function getAllRecordObjects() : array {
        return $this->getRecordObjects(0, 50000);
    }

    public function getJSDatalist() : JSDatalist {
        return new JSDatalist($this->setKey . "_dataset",
            $this->getAllRecordObjects());
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

    public function isEntityReceivedFromUrl() : bool {
        return isset($_GET[$this->urlParamEntityKey]);
    }

    public function getCurrentRecordPrimaryKey() : string | null {
        if($this->isEntityReceivedFromUrl()) return $_GET[$this->urlParamEntityKey];
        return null;
    }

    public function getCurrentRecord(){
        $pk = $this->getCurrentRecordPrimaryKey();
        if($pk){
            $this->latestFetchedRecord = $this->getRecord($pk);
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

        $this->conn->query($this->updateRecordQuery($pk, $data));
        return $this->getRecord($pk);
    }

    public function deleteRecord($pk) : bool {
        return $this->conn->query($this->deleteRecordQuery($pk));
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
        $pk = $this->getCurrentRecordPrimaryKey();
        $record = $this->getCurrentRecord();
        if($pk && !$record) return;
        $curRecordObject = $this->getRecordObject($record);

        HTMLInterface::placeVerticalDivider(12);
        echo '<div style="width: 80%; margin: auto;">';

        $dataSets = $this->getDataSets();
        foreach ($dataSets as $dataSet){
            $dataSet->place();
        }

        echo '<form ';
        HTMLInterface::addAttribute("method", "post");
        HTMLInterface::addAttribute("name", $this->getFormId());
        HTMLInterface::addAttribute("id", $this->getFormId());
        HTMLInterface::addAttribute("enctype", "multipart/form-data");
        HTMLInterface::addAttribute("onsubmit", $this->jsValidateForm());
        HTMLInterface::closeTag();

        echo '<fieldset><legend>Insert & Update</legend>';

        if($pk) {
            echo '<input type="hidden" name="entity_pk" id="entity_pk" value="' . $pk . '" />';
        }
        HTMLInterface::placeHiddenField($this->getFormTriggerElementId(), "");

        foreach ($this->dataFields() as $field){
            if($field->writable === True || ($field->writable && !$pk) ){
                $fieldType = $field->type;
                $title = $field->title;
                $key = $field->key;
                $value = isset($record[$key]) ? $record[$key] : null;

                if($field->avatar){
                    /** @var EntityAvatarField $avatarField */
                    $avatarField = $field;

                    $avExists = false;
                    $avBrowserSrc = "";
                    $avServerSrc = "";

                    if($record){
                        $avServerSrc = $avatarField->getServerSrc($record);
                        $avBrowserSrc = $avatarField->getBrowserSrc($record);
                        if(file_exists($avServerSrc)){
                            $avExists = true;
                        }
                    }

                    if($avExists && $avatarField->manualCrop){
                        $avatarField->presentCroppingImage($this, $curRecordObject);
                    }

                    $div = new NiceDiv(12);
                    $div->addStyle("margin-top", "8px");
                    $div->addStyle("margin-bottom", "8px");
                    $div->open();

                    if(!$avExists) {
                        HTMLInterface::placeText("$title: ");
                        $div->separate();
                    }

                    echo '<input ';
                    HTMLInterface::addAttribute("type", "file");
                    HTMLInterface::addAttribute("name", $key);
                    HTMLInterface::addAttribute("id", $key);
                    HTMLInterface::addAttribute("class", "empty");
                    Styler::startAttribute();
                    Styler::addStyle("font-size", "13pt");
                    Styler::closeAttribute();
                    HTMLInterface::closeSingleTag();

                    HTMLInterface::placePostInput($key, "", $title . " Url");

                    if($avExists && !$avatarField->manualCrop){
                        $avatarModifier = WebModifier::createInstance();
                        $avatarModifier->styler->pushStyle("margin-bottom", "8px");
                        HTMLInterface::placeImageWithHeight($avBrowserSrc . "?" . time(), 120,
                            $avatarModifier);
                        $div->separate();
                    }

                    $div->close();
                }
                else if($fieldType == "boolean"){
                    echo $title . ' ';
                    echo '<input ';
                    HTMLInterface::addAttribute("type", "checkbox");
                    HTMLInterface::addAttribute("value", "1");
                    HTMLInterface::addAttribute("name", $key);
                    HTMLInterface::addAttribute("id", $key);
                    if($value) HTMLInterface::addAttribute("checked", "true");
                    Styler::startAttribute();
                    Styler::addStyle("margin-bottom", "8px");
                    Styler::closeAttribute();
                    HTMLInterface::closeSingleTag();
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
                    HTMLInterface::addAttribute("name", $key);
                    HTMLInterface::addAttribute("id", $key);
                    HTMLInterface::addAttribute("rows", "8");
                    HTMLInterface::addAttribute("cols", "150");
                    HTMLInterface::closeTag();
                    echo $value;
                    echo '</textarea>';
                }
                else if($field->hidden){
                    echo '<input ';
                    HTMLInterface::addAttribute("type","hidden");
                    HTMLInterface::addAttribute("name", $key);
                    HTMLInterface::addAttribute("id", $key);
                    HTMLInterface::addAttribute("value", $value ? $value : "");
                    HTMLInterface::closeSingleTag();
                }
                else if($field instanceof EntityFlagField && $fieldType == "country"){
                    $catFactory = $field->countriesACFactory;
                    $catFactory->fieldKey = "countries-actext";
                    $catFactory->childKey = $key ? $key : "";

                    $csModifier = WebModifier::createInstance();
                    $csModifier->styler->pushStyle("margin-top", "12px");
                    $csModifier->styler->pushStyle("margin-bottom", "12px");

                    $countrySelector = new CountrySelector(
                        $key,
                        $catFactory,
                        "Select Nation",
                        true,
                        $value ? $value : ""
                    );
                    $countrySelector->setNameIdentifier = true;
                    $countrySelector->place($csModifier);
                }
                else if($field instanceof EntitySelectField && $fieldType == "select"){
                    $sModifier = WebModifier::createInstance();
                    $sModifier->styler->pushStyle("margin-top", "8px");
                    $sModifier->styler->pushStyle("margin-bottom", "8px");
                    $selectField = new JSDynamicSelect($field->title, $key, $value, $field->dataSetKey);
                    $selectField->setNameIdentifier = true;
                    $selectField->place($sModifier);
                }
                else if($field instanceof EntityCodingField && $fieldType == EntityCodingField::CodingFieldType){
                    $field->place($curRecordObject);
                }
                else {
                    $classApplier = new Styler();
                    $classApplier->pushClass("empty");
                    if($field->numeric) $classApplier->pushClass("numeric-text");

                    echo '<input ';
                    HTMLInterface::addAttribute("type","text");
                    HTMLInterface::addAttribute("name", $key);
                    HTMLInterface::addAttribute("id", $key);
                    HTMLInterface::addAttribute("value", $value ? $value : "");
                    HTMLInterface::addAttribute("placeholder", $title);

                    Styler::classStartAttribute();
                    $classApplier->appendClasses();
                    Styler::closeAttribute();

                    Styler::startAttribute();
                    Styler::addStyle("width", "80%");
                    Styler::addStyle("font-size", "14pt");
                    Styler::addStyle("margin-top", "8px");
                    Styler::addStyle("margin-bottom", "8px");
                    Styler::addStyle("padding-left", "8px");
                    Styler::addStyle("padding-right", "8px");
                    Styler::addStyle("padding-top", "4px");
                    Styler::addStyle("padding-bottom", "4px");
                    if($field->rtl) {
                        Styler::addFontFaceStyle("IranSans");
                        Styler::addStyle("direction", "rtl");
                    }
                    Styler::closeAttribute();
                    HTMLInterface::closeSingleTag();
                }
            }
            else if($pk && $field->printable) {
                $key = $field->key;
                $value = isset($record[$key]) ? $record[$key] : null;

                echo '<div ';
                Styler::startAttribute();
                Styler::addStyle("margin-top", "6px");
                Styler::addStyle("margin-bottom", "6px");
                Styler::closeAttribute();
                HTMLInterface::closeTag();
                echo $field->title . ': ' . $value;
                HTMLInterface::closeDiv();
            }
        }

        $this->formMainExtension($options);
        echo '</fieldset>';

        $this->formMoreExtension($options);

        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle("text-align", "center");
        Styler::addStyle("margin-top", "24px");
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        $submitModifier = WebModifier::createInstance();
        $submitModifier->htmlModifier->pushModifier("name", "entity_form");
        FormUtils::placeSubmitButton("Submit", "", 8, $submitModifier);

        if($record && $this->deletable){
            $deleteButton = new AbsoluteFormButton($this->getFormId(),
                $this->getDeleteTriggerKey(), ["right" => "20px", "bottom" => "20px"],
                AssetsManager::getImage("remove.svg"),
                $this->getFormTriggerElementId());
            $deleteButton->confirmMessage = "Are you sure to delete this "
                . strtolower($this->entityName) . "?";
            $deleteButton->place();
        }

        HTMLInterface::closeDiv();
        echo '</form>';
    }

    public function handleForm($options = null){
        $justNewRecordInserted = false;
        $newRecordUrl = "";

        if(isset($_POST['entity_form'])){
            $data = $_POST;
            $entityPk = isset($data['entity_pk']) ? $data['entity_pk'] : null;
            $currentRecord = $entityPk ? $this->getRecord($entityPk) : null;
            $curRecordObject = $this->getRecordObject($currentRecord);

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

            if($currentRecord){
                $this->adjustUpdateData($data, $currentRecord, $options);
                $this->updateRecord($entityPk, $data);
            }
            else {
                $this->adjustCreateData($data, $options);
                $currentRecord = $this->insertRecord($data);
                if ($currentRecord) {
                    $entityPk = $currentRecord[$this->getSuperKey()];
                    echo "Registered Successfully (";
                    $this->printEntityLinkedName($currentRecord);
                    echo ')' . br();

                    if($this->redirectOnInsert) {
                        $newRecordUrl = Routing::addParamToCurrentLink($this->urlParamEntityKey, $entityPk);
                        $justNewRecordInserted = true;
                    }
                }
            }

            if($entityPk && count($avatarFields) > 0){
                foreach ($avatarFields as $af){
                    /** @var EntityAvatarField $avatarField */
                    $avatarField = $af;

                    $up = false;
                    $targetFilename = $avatarField->noExtServerSrc($currentRecord);
                    if(!empty($_FILES[$af->key]['name'])){
                        $imageDetails = $_FILES[$af->key];
                        $tmpFilename = $imageDetails['tmp_name'];
                        $orgName = $imageDetails['name'];
                        $orgExtension = Filer::getFileExtension($orgName);
                        $targetFilename .= ('.' . $orgExtension);
                        move_uploaded_file($tmpFilename, $targetFilename);
                        $up = true;
                    }
                    else if(!empty($data[$af->key])){
                        $avatarSrc = $data[$af->key];
                        $orgExtension = Filer::getFileExtension($avatarSrc);
                        if($orgExtension) $targetFilename .= ('.' . $orgExtension);
                        $this->getNetworkFetcher()->downloadFile($avatarSrc, $targetFilename);
                        $up = true;
                    }

                    if($up){
                        $extensionRequired = ($orgExtension && $orgExtension != $af->targetExt);
                        $convertRequired = $extensionRequired;
                        if(!$convertRequired && $af->maxImageSize){
                            $curMaxSize = ImageUtils::getMaxDimSize($targetFilename);
                            if($curMaxSize > $af->maxImageSize) $convertRequired = true;
                        }

                        $finalWD = $avatarField->manualCrop ? 0 : $af->forcedWidthDimension;
                        $finalHD = $avatarField->manualCrop ? 0 : $af->forcedHeightDimension;

                        if(!$convertRequired && $finalWD > 0 && $finalHD > 0){
                            $curDiff = ImageUtils::getRatioDiffWithDims($targetFilename,
                                $af->forcedWidthDimension, $af->forcedHeightDimension);
                            if($curDiff > 0.01) $convertRequired = true;
                        }


                        if($convertRequired) {
                            ImageUtils::convert($targetFilename,
                                $extensionRequired ? $af->targetExt : null,
                                $af->maxImageSize, $finalWD, $finalHD);
                        }
                    }
                    else if($curRecordObject) {
                        $crImage = $avatarField->getCroppingImage($this, $curRecordObject);
                        if($crImage) $crImage->checkSubmit();
                    }
                }
            }

            $this->manualHandleForm();
        }

        if($justNewRecordInserted){
            JSInterface::redirect($newRecordUrl, 500);
            die;
        }

        if(!empty($_POST[$this->getFormTriggerElementId()])){
            $trigger = $_POST[$this->getFormTriggerElementId()];
            if($trigger == $this->getDeleteTriggerKey() && $this->deletable){
                $currentRecord = $this->fastCurrentRecord();

                if($currentRecord){
                    $entityPk = $this->getItemId($currentRecord);
                    if($this->deleteRecord($entityPk)){
                        HTMLInterface::placeVerticalDivider(8);
                        $this->deleteRecordResources($currentRecord);
                        $recordName = $this->getItemTitle($currentRecord);
                        Printer::warningPrint($recordName . ": Deleted");
                        endline();
                        JSInterface::redirect(Routing::currentPureLink(), 1000);
                        die;
                    }
                }
            }
        }
    }

    protected function deleteRecordResources($record){
        $entityItem = $this->getRecordObject($record);
        if($entityItem){
            $entityItem->deleteAllResources();
        }
    }

    public function createEntityItem(array $record) : SBEntityItem | array {
        if($this->entityModel) return SBEntityItem::createInstance($this->entityModel, $record);
        return $record;
    }

    public function getRecordObject(SBEntityItem|array|null $record) : SBEntityItem | array | null {
        if($record instanceof SBEntityItem) return $record;
        if(is_array($record)) return $this->createEntityItem($record);
        return null;
    }

    public function getCurrentRecordObject() : SBEntityItem | array | null {
        $record = $this->getCurrentRecord();
        if(!$record) return null;
        return $this->getRecordObject($record);
    }

    //extension inside main legend
    public function formMainExtension($options){}

    //extension in new legends
    public function formMoreExtension($options){}

    public function manualHandleForm(){}

    /** @return JSDataElement[] */
    public function getDataSets() : array {
        return [];
    }

    public function entityPage() : string {
        return "";
    }

    public function jsValidateForm() : string {
        return "return true;";
    }

    public function adjustCreateData(&$data, $options = null){}

    public function adjustUpdateData(&$data, $record, $options = null){}

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
        $title = $record ? $this->getPageTitle($record) : ("Add " . $this->entityName);

        $theme = $this->getTheme();
        $theme->placeHeader($title);
        $theme->loadHeaderElements();
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
        return $this->getItemTitle($item);
    }

    public function getFormId() : string {
        return "entity_patch_form";
    }

    public function getFormTriggerElementId() : string {
        return "form_trigger_data";
    }

    public function getDeleteTriggerKey() : string {
        return "delete_entity";
    }

    abstract public function getSuperKey();
    abstract public function getTableName();

    public function entityWhereFields() : array {
        return [];
    }

    /** @return EntityField[] */
    abstract public function dataFields() : array;
}
