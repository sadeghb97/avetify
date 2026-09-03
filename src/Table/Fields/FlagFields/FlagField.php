<?php
namespace Avetify\Table\Fields\FlagFields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Interface\HTML\HTMLInterface;
use Avetify\Interface\HTML\HTMLModifier;
use Avetify\Interface\WebModifier;
use Avetify\Repo\Countries\World;
use Avetify\Table\Fields\TableField;

class FlagField extends TableField {
    public function presentValue($item, ?WebModifier $webModifier = null) {
        $countryCodesRaw = $this->getValue($item);
        $countryCodes = !$countryCodesRaw ? [] : explode(",", $countryCodesRaw);

        $horizDiv = new NiceDiv();
        $horizDiv->open();

        foreach ($countryCodes as $countryCode){
            $country = World::getCountry($countryCode);
            $flag = World::getCountryFlag($countryCode);

            if($flag){
                $countryLink = $this->getCountryLink($country);

                if($countryLink){
                    echo '<a ';
                    HTMLInterface::addAttribute("href", $countryLink);
                    HTMLInterface::addAttribute("target", "_blank");
                    HTMLInterface::closeTag();
                }

                if(!$webModifier) $webModifier = WebModifier::createInstance();
                if(!$webModifier->htmlModifier) $webModifier->htmlModifier = new HTMLModifier();
                $webModifier->htmlModifier->pushModifier("title", $country['short_name']);
                HTMLInterface::placeImageWithHeight($flag, 50, $webModifier);

                if($countryLink) HTMLInterface::closeLink();
            }
        }

        HTMLInterface::closeDiv();
    }

    public function getCountryLink($country): string {
        return "?nationality=" . $country['alpha2'];
    }
}


