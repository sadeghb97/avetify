<?php
namespace Avetify\Components\DialogFields;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

abstract class DialogField implements Placeable {
    public bool $isFlex = true;

    public function __construct(public string $id, public string $title, public string $value){}

    public abstract function present();

    public function initJS(){
        ?>
        <script>
            document.getElementById("<?php echo $this->getBoxId(); ?>").addEventListener("click", function(event) {
                const fieldElement = document.getElementById("<?php echo $this->id; ?>")
                const displayElement = document.getElementById("<?php echo $this->getSpanId(); ?>")
                const curValue = fieldElement.value
                const newValue = prompt("Enter new <?php echo $this->title; ?>: ", curValue)
                if(newValue === "0" || (newValue && !isNaN(newValue))){
                    fieldElement.value = newValue
                    displayElement.innerText = newValue
                }
            });
        </script>
        <?php
    }

    public function place(WebModifier $webModifier = null){
        echo '<div id="' . $this->getBoxId() . '" ';
        Styler::startAttribute();
        $this->boxStyles();
        Styler::closeAttribute();
        echo ' >';
        $this->present();
        echo '</div>';
        $this->initJS();
    }

    public function boxStyles(){
        if($this->isFlex) Styler::addStyle("display", "flex");
        else Styler::addStyle("display", "block");
        Styler::addStyle("align-items", "center");
        Styler::addStyle("justify-content", "center");
        Styler::addStyle("margin-top", "3px;");
        Styler::addStyle("margin-bottom", "3px;");
    }

    public function setNameAndIdAttribute(){
        HTMLInterface::addAttribute("id", $this->id);
    }

    public function placeHiddenValue(){
        echo '<input type="hidden" ';
        $this->setNameAndIdAttribute();
        echo ' value="';
        echo $this->value;
        echo '" >';
    }

    public function getBoxId() : string {
        return self::getBoxStarterId() . $this->id;
    }

    public static function getBoxStarterId() : string {
        return "box_";
    }

    public function getSpanId() : string {
        return self::getSpanStarterId() . $this->id;
    }

    public static function getSpanStarterId() : string {
        return "span_";
    }
}






