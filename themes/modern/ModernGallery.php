<?php

abstract class ModernGallery extends ModernSetRenderer {
    public RecordContextMenu | null $contextMenu = null;

    public function __construct(SetModifier $setModifier, public string $title){
        parent::__construct($setModifier);
    }

    public function renderRecord($item, $index) {
        printCard($this->getItemImage($item), "", "", $this->getItemLink($item),
            $this->getCardOptions($item));
    }

    public function getCardOptions($item) : array {
        $options = [];
        if($this->contextMenu != null){
            $options['context_menu'] = $this->contextMenu;
            $options['cmr_id'] = $this->setModifier->getItemId($item);
        }
        return $options;
    }

    public function getTitle(): string {
        return $this->title;
    }

    abstract public function getItemImage($record) : string;

    public function getItemLink($record) : string | null {
        return null;
    }
}