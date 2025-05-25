<?php
namespace Avetify\Fields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\WebModifier;

class JSDynamicSelect implements Placeable {
    public bool $setNameIdentifier = false;

    public function __construct(public string $title, public string $elementId,
                                public string|null $value, public string $dataSetId){
        if(!$this->value) $this->value = "";
    }


    public function place(WebModifier $webModifier = null) {
        $div = new NiceDiv(8);
        $div->open($webModifier);

        if($this->title) {
            HTMLInterface::placeText($this->title . ': ');
            $div->separate();
        }

        echo '<select ';
        HTMLInterface::addAttribute("id", $this->elementId);
        if($this->setNameIdentifier) HTMLInterface::addAttribute("name", $this->elementId);
        HTMLInterface::closeTag();
        echo '</select>';
        $div->close();

        ?>
            <script>
                (function() {
                    const fieldElement = document.getElementById("<?php echo $this->elementId; ?>");
                    if(!fieldElement) return;
                    const fieldChildCount = fieldElement.children.length

                    if(fieldChildCount <= 0){
                        const template = document.getElementById('<?php echo $this->dataSetId; ?>').content;
                        fieldElement.appendChild(template.cloneNode(true));
                        fieldElement.value = '<?php echo $this->value; ?>';
                    }
                })();
            </script>
        <?php
    }
}
