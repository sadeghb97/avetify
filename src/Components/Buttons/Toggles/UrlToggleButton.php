<?php
namespace Avetify\Components\Buttons\Toggles;

use Avetify\AvetifyManager;
use Avetify\Network\URLBuilder;
use Avetify\Routing\Routing;

class UrlToggleButton extends ToggleButton {
    public string $nextPage = "";

    public function __construct(public array $pages, array $positionStyles) {
        parent::__construct($positionStyles);
        $this->imageSrc = AvetifyManager::imageUrl("view_alt.svg");
        $curScript = Routing::currentScriptName();
        $key = array_search($curScript, $this->pages);

        if($key === false){
            $this->nextPage = $this->pages[0];
        }
        else {
            $nextIndex = $key + 1;
            if($nextIndex >= count($this->pages)) $nextIndex = 0;
            $this->nextPage = $this->pages[$nextIndex];
        }
    }

    public function buildNextPageUrl() : string {
        $urlBuilder = URLBuilder::fromCurrent();
        return $urlBuilder->buildUrl($this->nextPage);
    }
}
