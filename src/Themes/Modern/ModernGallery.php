<?php
namespace Avetify\Themes\Modern;

use Avetify\Entities\ContextMenus\RecordContextMenu;
use Avetify\Fields\APIMedalField;
use Avetify\Fields\APISpanField;
use Avetify\Fields\JSTextFields\APITextField;

class ModernGallery extends ModernSetRenderer {
    public RecordContextMenu | null $contextMenu = null;
    public bool $openMenuOnNormalClick = false;
    public bool $stopPropagationOnBody = false;

    public function renderRecordMain($item, $index) {
        $name = ($index + 1) . ": " . $this->setManager->getItemTitle($item);
        ModernThemeBadCards::printCard($this->setManager->getItemImage($item), $name, "",
            $this->setManager->getItemLink($item),
            $this->getCardOptions($item));
    }

    public function getCardOptions($item) : array {
        $options = [];
        $nationsRaw = $this->getNationsRaw($item);
        if($nationsRaw){
            $options['nations'] = $nationsRaw;
        }

        if($this->stopPropagationOnBody){
            $options['stop_propagation_on_body'] = true;
        }

        if($this->contextMenu != null){
            $options['context_menu'] = $this->contextMenu;
            $options['context_menu_on_click'] = $this->openMenuOnNormalClick;
            $options['cmr_id'] = $this->setManager->getItemId($item);
        }
        $altLink = $this->setManager->getItemAltLink($item);
        $altLinkIcon = $this->getAltLinkIcon();
        if($altLink && $altLinkIcon){
            $options['icon_link'] = ["link" => $altLink, "icon" => $altLinkIcon];
        }

        $moreItemLinks = $this->getMoreItemIconLinks($item);
        $options['more_icon_links'] = $moreItemLinks;

        if($this->smallerTitle()){
            $options['smaller_title'] = true;
        }

        $medals = $this->getMedals($item);
        if(count($medals) > 0){
            $options['medals'] = $medals;
        }

        $apiMedals = $this->getApiMedals($item);
        if(count($apiMedals) > 0){
            $options['api_medals'] = $apiMedals;
        }

        $spanTexts = $this->getSpanTexts($item);
        $recordId = $this->setManager->getItemId($item);
        $options['span_texts'] = $spanTexts;
        $options['stpk'] = $recordId;

        $apiTexts = $this->getApiTexts($item);
        if(count($apiTexts) > 0){
            $options['api_texts'] = $apiTexts;
        }

        $rarity = $this->getRarity($item);
        if($rarity){
            $options['magham'] = $rarity;
        }

        return $options;
    }

    public function getNationsRaw($record) : string {
        return "";
    }

    /** @return ModernGallery[] */
    public function getMedals($record) : array {
        return [];
    }

    /** @return APIMedalField[] */
    public function getApiMedals($record) : array {
        return [];
    }

    /** @return APITextField[] */
    public function getApiTexts($record) : array {
        return [];
    }

    /** @return APISpanField[] */
    public function getSpanTexts($record) : array {
        return [];
    }

    public function getRarity($record) : string {
        return "";
    }

    public function getPageTitle(): string {
        return $this->pageTitle;
    }

    public function getAltLinkIcon(): string {
        return "";
    }

    public function getMoreItemIconLinks($record) : array {
        return [];
    }

    public function smallerTitle() : bool {
        return false;
    }
}