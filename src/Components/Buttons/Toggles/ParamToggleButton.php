<?php
namespace Avetify\Components\Buttons\Toggles;

use Avetify\AvetifyManager;
use Avetify\Network\URLBuilder;
use Avetify\Routing\Routing;

class ParamToggleButton extends ToggleButton {
    public function __construct(public string $param, array $positionStyles) {
        parent::__construct($positionStyles);
    }

    public function buildNextPageUrl() : string {
        $urlBuilder = URLBuilder::fromCurrent();
        if(isset($_GET[$this->param])) $urlBuilder->removeParam($this->param);
        else $urlBuilder->addParam($this->param, "1");
        return $urlBuilder->buildUrl();
    }
}
