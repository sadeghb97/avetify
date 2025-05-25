<?php
namespace Avetify\Themes\Main\Navigations;

use Avetify\Interface\WebModifier;
use Avetify\Models\Detailed;

class NavigationLink extends Detailed {
    public ?WebModifier $modifier = null;
    public array $params = [];

    public function __construct(public string $title, public string $link, public bool $isActive = false) {}

    public function addParam(string $key, string $value) : NavigationLink {
        $this->params[$key] = $value;
        return $this;
    }
}