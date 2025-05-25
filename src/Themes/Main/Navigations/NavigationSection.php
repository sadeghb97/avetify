<?php
namespace Avetify\Themes\Main\Navigations;

use Avetify\Interface\WebModifier;
use Avetify\Models\Detailed;

class NavigationSection extends Detailed {
    public ?WebModifier $modifier = null;
    /** @var NavigationLink[] */
    public array $menuLinks = [];

    public function pushLink(NavigationLink $link){
        $this->menuLinks[] = $link;
    }
}