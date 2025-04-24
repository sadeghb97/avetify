<?php

abstract class SBTRenderer extends SetRenderer {
    public SBForm | null $form = null;
    public RecordSelectorField | null $selectorField = null;
    public bool $printRowIndex = true;
    public bool $useClassicButtons = false;


    public function __construct(SBTable $setModifier,
                                public string $title = "Set", bool|int $limit = 5000){
        $theme = $this->getTheme();
        parent::__construct($setModifier, $theme, $limit);
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function initForm(){
        /** @var SBTable $sbTable */
        $sbTable = $this->setModifier;

        $this->form = new SBForm($this->getFormName());
        $this->form->addHiddenElement(new FormHiddenProperty($this->getFormFieldsName(), ""));
        $this->form->addHiddenElement(new FormHiddenProperty($this->getFormSelectorName(), ""));

        $deleteConfirmMessage = "Are you sure?";
        if($this->useClassicButtons) {
            $this->form->addTrigger(new FormButton($this->getFormName(), $this->getUpdateButtonID(),
                "Update"));

            $deleteTrigger = new FormButton($this->getFormName(), $this->getDeleteButtonID(),
                "Delete", "warning");
            $deleteTrigger->enableConfirmMessage($deleteConfirmMessage);
            $this->form->addTrigger($deleteTrigger);
        }
        else {
            $this->form->addTrigger(new PrimaryFormButton($this->getFormName(), $this->getUpdateButtonID()));

            $deleteTrigger = new DeleteFormButton($this->getFormName(), $this->getDeleteButtonID());
            $deleteTrigger->enableConfirmMessage($deleteConfirmMessage);
            $this->form->addTrigger($deleteTrigger);
        }
    }

    public function placeJSUtils(){
        /** @var SBTable $sbTable */
        $sbTable = $this->setModifier;

        if($sbTable->isEditable) {
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
    }

    public function renderSortLabels(){
        /** @var SBTable $sbTable */
        $sbTable = $this->setModifier;

        $allSortFactors = $sbTable->finalSortFactors();
        echo '<div style="text-align: center; margin-top: 2px;">';
        $defaultBg = 'Black';
        $defaultColor = 'Cyan';
        $alterBg = 'Black';
        $alterColor = 'GoldenRod';
        printLabel("Clear", Routing::removeParamFromCurrentLink($sbTable->getSortKey()), $defaultBg, $defaultColor);

        $currentSort = isset($_GET[$sbTable->getSortKey()]) ? $_GET[$sbTable->getSortKey()] : null;
        $startWithMinus = $currentSort ? str_starts_with($currentSort, "-") : false;
        $pureSort = null;
        if($currentSort != null){
            $pureSort = $startWithMinus ? substr($currentSort, 1) : $currentSort;
        }

        foreach ($allSortFactors as $sortFactor){
            $finalBg = $defaultBg;
            $finalColor = $defaultColor;
            $finalTitle = $sortFactor->title;
            $nextDescending = $sortFactor->descIsDefault;
            if($currentSort && $pureSort == $sortFactor->factorKey){
                $nextDescending = !$startWithMinus;
                $finalBg = $alterBg;
                $finalColor = $alterColor;
                $finalTitle .= ($startWithMinus ? " ↓" : " ↑");
            }
            $finalSortFactor = ($nextDescending ? "-" : "") . $sortFactor->factorKey;

            printLabel($finalTitle, Routing::addParamToCurrentLink($sbTable->getSortKey(),
                $finalSortFactor), $finalBg, $finalColor);
        }

        echo '</div>';
    }

    public function openContainer() {
        /** @var SBTable $sbTable */
        $sbTable = $this->setModifier;

        $this->initForm();
        echo '<div style="height: ' . 8 . 'px;"></div>';

        if($sbTable->isEditable) $sbTable->catchSubmittedFields();
        if($sbTable->isSortable) $this->renderSortLabels();

        if($sbTable->isEditable){
            $this->form->openForm();
        }
    }

    public function closeContainer() {
        /** @var SBTable $sbTable */
        $sbTable = $this->setModifier;

        if($sbTable->isEditable) {
            $this->form->placeTriggers();
            $this->form->closeForm();
        }
        $this->placeJSUtils();
    }

    public function renderCreatingElements(){}

    public function getTheme() : ThemesManager {
        return new GreenTheme();
    }

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
