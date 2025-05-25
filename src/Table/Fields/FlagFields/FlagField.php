<?php
namespace Avetify\Table\Fields\FlagFields;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\WebModifier;
use Avetify\Repo\Countries\World;
use Avetify\Table\Fields\TableField;

class FlagField extends TableField {
    public function presentValue($item) {
        $countryCode = $this->getValue($item);
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

            $flagModifier = WebModifier::createInstance();
            $flagModifier->htmlModifier->pushModifier("title", $country['short_name']);
            HTMLInterface::placeImageWithHeight($flag, 50, $flagModifier);

            if($countryLink) HTMLInterface::closeLink();
        }
    }

    public function getCountryLink($country): string {
        return "";
    }
}


