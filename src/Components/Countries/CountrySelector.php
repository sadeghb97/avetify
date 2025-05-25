<?php
namespace Avetify\Components\Countries;

use Avetify\Components\NiceDiv;
use Avetify\Fields\JSACTextField;
use Avetify\Fields\JSDatalist;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\HTMLModifier;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;
use Avetify\Repo\Countries\World;

class CountrySelector implements Placeable {
    public bool $setNameIdentifier = false;

    public function __construct(public string $mainElementId,
                                public CountriesACTextFactory $countriesACFactory,
                                public string $label,
                                public bool $disableAutoSubmit,
                                public string $initCountryCode){
    }


    public function place(WebModifier $webModifier = null) {
        $countryDetails = World::getCountry($this->initCountryCode);
        $countryFlag = World::getCountryFlag($this->initCountryCode);
        $countryName = $countryDetails ? $countryDetails['short_name'] : "";

        $div = new NiceDiv(6);
        $div->open($webModifier);

        echo '<input ';
        HTMLInterface::addAttribute("type", "hidden");
        if($countryDetails != null) {
            HTMLInterface::addAttribute("value", $this->initCountryCode);
        }
        HTMLInterface::addAttribute("id", $this->mainElementId);
        if($this->setNameIdentifier) HTMLInterface::addAttribute("name", $this->mainElementId);
        HTMLInterface::closeSingleTag();

        $acTextField = $this->countriesACFactory->create();
        $acTextField->label = $this->label;
        $acTextField->place();
        $div->separate();

        $countryLink = "";
        $preLink = $acTextField->getPreCountryLink();
        $postLink = $acTextField->getPostCountryLink();
        if($preLink || $postLink){
            $countryLink = $preLink . $this->initCountryCode . $postLink;
        }

        if($countryLink){
            echo '<a ';
            HTMLInterface::addAttribute("href", $countryLink);
            HTMLInterface::addAttribute("id", $this->mainElementId . "_link");
            HTMLInterface::addAttribute("target", "_blank");
            HTMLInterface::closeTag();
        }

        $flagModifier = new WebModifier(new HTMLModifier(), null);
        $flagModifier->htmlModifier->pushModifier("id", $this->mainElementId . "_flag");
        $flagModifier->htmlModifier->pushModifier("title", $countryName);
        HTMLInterface::placeImageWithHeight($countryFlag ? $countryFlag : "", 50, $flagModifier);

        if($countryLink) HTMLInterface::closeLink();

        $div->close();
    }
}






