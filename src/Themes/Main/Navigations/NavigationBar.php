<?php
namespace Avetify\Themes\Main\Navigations;

use Avetify\Interface\WebModifier;
use Avetify\Models\Detailed;

class NavigationBar extends Detailed {
    public ?WebModifier $modifier = null;
    /** @var NavigationSection[] */
    public array $sections = [];

    public function pushSection(NavigationSection $section){
        $this->sections[] = $section;
    }
}