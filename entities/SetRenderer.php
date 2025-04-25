<?php

abstract class SetRenderer extends BaseSetRenderer {
    public SBForm | null $form = null;
    public RecordSelectorField | null $selectorField = null;
    public bool $printRowIndex = true;
    public bool $useClassicButtons = false;


    public function __construct(SetModifier $setModifier, null | ThemesManager $theme,
                                public string $title = "Set", bool|int $limit = 5000){
        if($theme == null) $theme = new GreenTheme();
        parent::__construct($setModifier, $theme, $limit);
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function initForm(){
        $this->form = new SBForm($this->getFormName());
        $this->form->addHiddenElement(new FormHiddenProperty($this->getFormFieldsName(), ""));
        $this->form->addHiddenElement(new FormHiddenProperty($this->getFormSelectorName(), ""));

        if($this->setModifier instanceof SBTable && $this->setModifier->isEditable) {
            $deleteConfirmMessage = "Are you sure?";
            $deleteButtonRequired = $this->setModifier->enableSelectRecord;
            if ($this->useClassicButtons) {
                $this->form->addTrigger(new FormButton($this->getFormName(), $this->getUpdateButtonID(),
                    "Update"));

                if ($deleteButtonRequired) {
                    $deleteTrigger = new FormButton($this->getFormName(), $this->getDeleteButtonID(),
                        "Delete", "warning");
                    $deleteTrigger->enableConfirmMessage($deleteConfirmMessage);
                    $this->form->addTrigger($deleteTrigger);
                }
            }
            else {
                $this->form->addTrigger(new PrimaryFormButton($this->getFormName(), $this->getUpdateButtonID()));

                if ($deleteButtonRequired) {
                    $deleteTrigger = new DeleteFormButton($this->getFormName(), $this->getDeleteButtonID());
                    $deleteTrigger->enableConfirmMessage($deleteConfirmMessage);
                    $this->form->addTrigger($deleteTrigger);
                }
            }
        }
    }

    public function placeFormJSUtils(){
        if(!($this->setModifier instanceof SBTable)) return;

        /** @var SBTable $sbTable */
        $sbTable = $this->setModifier;

        if(!$sbTable->isEditable) return;

        $allJSEditableFields = [];
        foreach ($sbTable->fields as $field) {
            foreach ($sbTable->currentRecords as $record) {
                if ($field instanceof SBEditableField) {
                    $allJSEditableFields[] = $field->getEditableFieldIdentifier($record);
                }
                else if($sbTable->forcePatchRecords && $field->onCreateField != null){
                    $feField = $field->getForcedEditableClone();
                    $allJSEditableFields[] = $feField->getEditableFieldIdentifier($record);
                }
            }

        }

        $allSelectFields = null;
        if($this->selectorField != null){
            $allSelectFields = [];
            foreach ($sbTable->currentRecords as $record) {
                $allSelectFields[] = $this->selectorField->getEditableFieldIdentifier($record);
            }
        }

        JSInterface::declareGlobalJSArgs($this->getJSArgsName());
        FormUtils::readyFormToCatchNoNamedFields(
            $this->getJSArgsName(),
            $this->getFormName(),
            $this->getFormFieldsName(),
            json_encode($allJSEditableFields),
            $sbTable->isEditable,
            $sbTable->enableSelectRecord ? $this->getFormSelectorName() : null,
            $allSelectFields
        );
    }

    public function openContainer() {
        echo '<div style="height: ' . 8 . 'px;"></div>';

        if($this->setModifier instanceof SBTable) {
            /** @var SBTable $sbTable */
            $sbTable = $this->setModifier;

            $this->initForm();

            if ($sbTable->isEditable) $sbTable->catchSubmittedFields();

            if ($sbTable->isEditable) {
                $this->form->openForm();
            }
        }
    }

    public function closeContainer() {
        if($this->setModifier instanceof SBTable) {
            /** @var SBTable $sbTable */
            $sbTable = $this->setModifier;

            if ($sbTable->isEditable) {
                $this->form->placeTriggers();
                $this->form->closeForm();
                $this->placeFormJSUtils();
            }
        }
    }

    public function renderCreatingElements(){}

    public function getFormName() : string {
        return $this->setModifier->setKey . "_" . "table_form";
    }

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
