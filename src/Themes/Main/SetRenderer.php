<?php
namespace Avetify\Themes\Main;

use Avetify\Entities\SetModifier;
use Avetify\Forms\AvtForm;
use Avetify\Forms\Buttons\DeleteFormButton;
use Avetify\Forms\Buttons\FormButton;
use Avetify\Forms\Buttons\PrimaryFormButton;
use Avetify\Forms\FormHiddenProperty;
use Avetify\Forms\FormUtils;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\JSInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Table\AvtTable;
use Avetify\Table\Fields\EditableFields\EditableField;
use Avetify\Table\Fields\EditableFields\RecordSelectorField;
use Avetify\Themes\Green\GreenTheme;

abstract class SetRenderer extends BaseSetRenderer {
    public AvtForm | null $form = null;
    public RecordSelectorField | null $selectorField = null;
    public bool $blankLink = true;
    public bool $printRowIndex = true;
    public bool $useClassicButtons = false;


    public function __construct(SetModifier $setModifier, null | ThemesManager $theme,
                                public string $title = "Set", bool|int $limit = 5000){
        if($theme == null) $theme = new GreenTheme();
        parent::__construct($setModifier, $theme, $limit);
        $this->containerModifier = $this->getFormModifier();
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function initForm(){
        $this->form = new AvtForm($this->getFormIdentifier());
        $this->form->addHiddenElement(new FormHiddenProperty($this->getFormFieldsName(), ""));
        $this->form->addHiddenElement(new FormHiddenProperty($this->getFormSelectorName(), ""));

        if($this->setModifier instanceof AvtTable && $this->setModifier->isEditable) {
            $deleteButtonRequired = $this->setModifier->enableSelectRecord;
            if ($this->useClassicButtons) {
                $this->addClassicUpdateTrigger($this->getFormIdentifier(), $this->getUpdateButtonID());

                if ($deleteButtonRequired) {
                    $this->addClassicDeleteTrigger($this->getFormIdentifier(), $this->getDeleteButtonID());
                }
            }
            else {
                $this->addUpdateTrigger($this->getFormIdentifier(), $this->getUpdateButtonID());

                if ($deleteButtonRequired) {
                    $this->addDeleteTrigger($this->getFormIdentifier(), $this->getDeleteButtonID());
                }
            }
        }
    }

    function addUpdateTrigger($formId, $buttonId){
        $this->form->addTrigger(new PrimaryFormButton($formId, $buttonId));
    }

    function addDeleteTrigger($formId, $buttonId){
        $deleteConfirmMessage = $this->getConfirmMessage();
        $deleteTrigger = new DeleteFormButton($formId, $buttonId);
        if($deleteConfirmMessage) $deleteTrigger->enableConfirmMessage($deleteConfirmMessage);
        $this->form->addTrigger($deleteTrigger);
    }

    function addClassicUpdateTrigger($formId, $buttonId){
        $this->form->addTrigger(new FormButton($formId, $buttonId,
            "Update"));
    }

    function addClassicDeleteTrigger($formId, $buttonId){
        $deleteConfirmMessage = $this->getConfirmMessage();
        $deleteTrigger = new FormButton($formId, $buttonId,
            "Delete", "warning");
        if($deleteConfirmMessage) $deleteTrigger->enableConfirmMessage($deleteConfirmMessage);
        $this->form->addTrigger($deleteTrigger);
    }

    function getConfirmMessage() : string {
        return "Are you sure?";
    }

    public function placeFormJSUtils(){
        if(!($this->setModifier instanceof AvtTable)) return;

        /** @var AvtTable $sbTable */
        $sbTable = $this->setModifier;

        if(!$sbTable->isEditable) return;

        $allJSEditableFields = [];
        foreach ($sbTable->getAllFields() as $field) {
            foreach ($sbTable->currentRecords as $record) {
                if ($field instanceof EditableField) {
                    $allJSEditableFields[] = $field->getElementIdentifier($record);
                }
                else if($sbTable->forcePatchRecords && $field->onCreateField != null){
                    $feField = $field->getForcedEditableClone();
                    $allJSEditableFields[] = $feField->getElementIdentifier($record);
                }
            }

        }

        $allSelectFields = null;
        if($this->selectorField != null){
            $allSelectFields = [];
            foreach ($sbTable->currentRecords as $record) {
                $allSelectFields[] = $this->selectorField->getElementIdentifier($record);
            }
        }

        JSInterface::declareGlobalJSArgs($this->getJSArgsName());
        FormUtils::readyFormToCatchNoNamedFields(
            $this->getJSArgsName(),
            $this->getFormIdentifier(),
            $this->getFormFieldsName(),
            json_encode($allJSEditableFields),
            $sbTable->isEditable,
            $sbTable->enableSelectRecord ? $this->getFormSelectorName() : null,
            $allSelectFields
        );
    }

    public function openContainer() {
        if($this->setModifier instanceof AvtTable) {
            /** @var AvtTable $sbTable */
            $sbTable = $this->setModifier;

            $this->initForm();

            if ($sbTable->isEditable) $sbTable->catchSubmittedFields();

            $sbTable->placeFormDataLists();

            foreach ($this->setModifier->fields as $field){
                if($field instanceof EditableField){
                    $field->preLoad();
                }
            }

            //$this->getFormModifier(): agar editable bashe baraye containere asli be kar mire
            //$this->containerModifier: agar editable nabashe baraye containere asli be kar mire
            if ($sbTable->isEditable) {
                $this->form->openForm($this->getFormModifier());
            }
            else {
                echo '<div ';
                Styler::classStartAttribute();
                HTMLInterface::appendClasses($this->containerModifier);
                Styler::closeAttribute();

                Styler::startAttribute();
                HTMLInterface::appendStyles($this->containerModifier);
                Styler::closeAttribute();

                HTMLInterface::applyModifiers($this->containerModifier);
                HTMLInterface::closeTag();
            }
        }
    }

    public function closeContainer() {
        if($this->setModifier instanceof AvtTable) {
            /** @var AvtTable $sbTable */
            $sbTable = $this->setModifier;

            if ($sbTable->isEditable) {
                $this->form->placeTriggers();
                $this->form->closeForm();
                $this->placeFormJSUtils();
            }
            else {
                HTMLInterface::closeDiv();
            }
        }
    }

    public function getFormModifier() : ?WebModifier {
        return WebModifier::createInstance();
    }

    public function renderCreatingElements(){}

    public function getFormFieldsName() : string {
        return $this->setModifier->setKey . "_" . "table_fields";
    }

    public function getFormSelectorName() : string {
        return $this->setModifier->setKey . "_" . "selector_field";
    }

    public function getJSArgsName() : string {
        return $this->setModifier->setKey . "_" . "args";
    }

    public function getUpdateButtonID() : string {
        return $this->setModifier->setKey . '_update';
    }

    public function getDeleteButtonID() : string {
        return $this->setModifier->setKey . '_delete';
    }
}
