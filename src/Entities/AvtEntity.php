<?php
namespace Avetify\Entities;

use Avetify\AvetifyManager;
use Avetify\DB\DBConnection;
use Avetify\Entities\Fields\EntityAvatarField;
use Avetify\Entities\Fields\EntityBooleanField;
use Avetify\Fields\JSDataElement;
use Avetify\Fields\JSDatalist;
use Avetify\Files\Filer;
use Avetify\Files\ImageUtils;
use Avetify\Forms\Buttons\AbsoluteFormButton;
use Avetify\Forms\FormUtils;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\JSInterface;
use Avetify\Interface\Pout;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Modules\Printer;
use Avetify\Network\NetworkFetcher;
use Avetify\Routing\Routing;
use Avetify\Themes\Green\GreenTheme;
use Avetify\Themes\Main\ThemesManager;

abstract class AvtEntity extends SetModifier {
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

    public function getItemId($record): string {
        $superKey = $this->getSuperKey();
        if($superKey) return EntityUtils::getSimpleValue($record, $superKey);
        return parent::getItemId($record);
    }

    /** @return AvtEntityItem[] */
    public function getRecordObjects(int $offset, int $limit) : array {
        $recs = $this->getRecords($offset, $limit);
        $dsRecords = [];
        foreach ($recs as $rec){
            $dsRecords[] = $this->createEntityItem($rec);
        }
        return $dsRecords;
    }

    /** @return AvtEntityItem[] */
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
        foreach ($fields as $key => $value) {
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

    public function printEntityLinkedName($record){
        echo '<a href="' . $this->entityPage() . '?' .
            $this->urlParamEntityKey . '=' . $this->getItemId($record) . '" target="_blank"';
        echo ' style="text-decoration: none; font-weight: bold;" >';
        echo '<span style="color: Black;">';
        echo $this->getItemTitle($record);
        echo '</sapn>';
        echo '</a>';
    }

    public function extendRecord(&$item){}

    public function printForm($options = null){
        $pk = $this->getCurrentRecordPrimaryKey();
        $record = $this->getCurrentRecord();
        if(!$record && (!$this->isPatchRecordEnabled() || $pk)) return;

        $curRecordObject = $this->getRecordObject($record);

        HTMLInterface::placeVerticalDivider(12);
        echo '<div style="width: 80%; margin: auto;">';

        $dataSets = $this->getDataSets();
        foreach ($dataSets as $dataSet){
            $dataSet->place();
        }

        if($this->isPatchRecordEnabled()) {
            echo '<form ';
            HTMLInterface::addAttribute("method", "post");
            HTMLInterface::addAttribute("name", $this->getFormId());
            HTMLInterface::addAttribute("id", $this->getFormId());
            HTMLInterface::addAttribute("enctype", "multipart/form-data");
            HTMLInterface::addAttribute("onsubmit", $this->jsValidateForm());
            HTMLInterface::closeTag();
        }

        echo '<fieldset><legend>Insert & Update</legend>';

        if($pk) {
            echo '<input type="hidden" name="entity_pk" id="entity_pk" value="' . $pk . '" />';
        }
        HTMLInterface::placeHiddenField($this->getFormTriggerElementId(), "");

        foreach ($this->dataFields() as $field){
            $field->presentValue($curRecordObject);
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

        if($this->isPatchRecordEnabled()) {
            $submitModifier = WebModifier::createInstance();
            $submitModifier->htmlModifier->pushModifier("name", "entity_form");
            FormUtils::placeSubmitButton("Submit", "", 8, $submitModifier);
        }

        if($record && $this->deletable){
            $deleteButton = new AbsoluteFormButton($this->getFormId(),
                $this->getDeleteTriggerKey(), ["right" => "20px", "bottom" => "20px"],
                AvetifyManager::imageUrl("remove.svg"),
                $this->getFormTriggerElementId());
            $deleteButton->confirmMessage = "Are you sure to delete this "
                . strtolower($this->entityName) . "?";
            $deleteButton->place();
        }

        HTMLInterface::closeDiv();

        if($this->isPatchRecordEnabled()) echo '</form>';
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
                if($field instanceof EntityBooleanField && $field->writable && !isset($data[$field->key])){
                    $data[$field->key] = 0;
                }
                else if($field->autoTimeCreate){
                    if(!$entityPk) $data[$field->key] = time();
                }
                else if($field->autoTimeUpdate){
                    $data[$field->key] = time();
                }
                else if($field instanceof EntityAvatarField){
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
                    echo ')' . Pout::br();

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
                        $crImage = $avatarField->getCroppingImage($curRecordObject);
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
                        Pout::endline();
                        JSInterface::redirect(Routing::currentPureLink(), 1000);
                        die;
                    }
                }
            }
        }
    }

    protected function deleteRecordResources($record){
        $entityItem = $this->getRecordObject($record);
        if($entityItem instanceof AvtEntityItem){
            $entityItem->deleteAllResources();
        }
    }

    public function createEntityItem(array $record) : AvtEntityItem | array {
        if($this->entityModel) return AvtEntityItem::createInstance($this->entityModel, $record);
        return $record;
    }

    public function getRecordObject(AvtEntityItem|array|null $record) : AvtEntityItem | array | null {
        if($record instanceof AvtEntityItem) return $record;
        if(is_array($record)) return $this->createEntityItem($record);
        return null;
    }

    public function getCurrentRecordObject() : AvtEntityItem | array | null {
        $record = $this->getCurrentRecord();
        if(!$record) return null;
        return $this->getRecordObject($record);
    }

    public function isPatchRecordEnabled() : bool {
        return true;
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
