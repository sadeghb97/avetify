<?php

abstract class ModernGallery extends ModernSetRenderer implements EntityImage, EntityTitle, EntityAltLink {
    public RecordContextMenu | null $contextMenu = null;

    public function __construct(SetModifier $setModifier, public string $title){
        parent::__construct($setModifier);
    }

    public function renderRecord($item, $index) {
        $name = ($index + 1) . ": " . $this->getItemTitle($item);
        printCard($this->getItemImage($item), $name, "",
            $this->getItemLink($item),
            $this->getCardOptions($item));
    }

    public function getCardOptions($item) : array {
        $options = [];
        if($this->contextMenu != null){
            $options['context_menu'] = $this->contextMenu;
            $options['cmr_id'] = $this->setModifier->getItemId($item);
        }
        $altLink = $this->getItemAltLink($item);
        $altLinkIcon = $this->getAltLinkIcon();
        if($altLink && $altLinkIcon){
            $options['icon_link'] = ["link" => $altLink, "icon" => $altLinkIcon];
        }
        if($this->smallerTitle()){
            $options['smaller_title'] = true;
        }

        $medals = $this->getMedals($item);
        if(count($medals) > 0){
            $options['medals'] = $medals;
        }
        return $options;
    }

    /** @return ModernGallery[] */
    public function getMedals($record) : array {
        return [];
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getItemLink($record): string {
        return "";
    }

    public function getItemAltLink($record): string {
        return "";
    }

    public function getAltLinkIcon(): string {
        return "";
    }

    public function smallerTitle() : bool {
        return false;
    }
}

class ModernGalleryMedal {
    public function __construct(public string $icon, public string $title,
                                public int $count, public string $link = ""){
    }
}