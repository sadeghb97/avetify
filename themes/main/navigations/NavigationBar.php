<?php

class NavigationBar extends Detailed {
    /** @var NavigationSection[] */
    public array $sections = [];

    public function pushSection(NavigationSection $section){
        $this->sections[] = $section;
    }
}

class NavigationSection extends Detailed {
    /** @var NavigationLink[] */
    public array $menuLinks = [];

    public function pushLink(NavigationLink $link){
        $this->menuLinks[] = $link;
    }
}

class NavigationLink extends Detailed {
    public array $params = [];
    public function __construct(public string $title, public string $link, public bool $isActive = false) {}

    public function addParam(string $key, string $value) : NavigationLink {
        $this->params[$key] = $value;
        return $this;
    }
}