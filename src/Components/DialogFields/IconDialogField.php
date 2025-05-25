<?php
namespace Avetify\Components\DialogFields;

use Avetify\Interface\Styler;

class IconDialogField extends DialogField {
    public string $imageHeight = "30px";
    public string $fontSize = "1rem";
    public string $sepSize = "2px";

    public function __construct(string $id, string $title, string $value, public string $src){
        parent::__construct($id, $title, $value);
    }

    public function present(){
        echo '<img src="' . $this->src . '" ';
        Styler::startAttribute();
        Styler::addStyle("height", $this->imageHeight);
        Styler::addStyle("width", "auto");
        Styler::closeAttribute();
        echo '>';

        echo '<span id="' . $this->getSpanId() . '" ';
        Styler::startAttribute();
        Styler::addStyle("margin-left", $this->sepSize);
        Styler::addStyle("margin-right", $this->sepSize);
        Styler::addStyle("font-size", $this->fontSize);
        Styler::closeAttribute();
        echo '>';
        echo $this->value;
        echo '</span>';

        $this->placeHiddenValue();
    }
}
