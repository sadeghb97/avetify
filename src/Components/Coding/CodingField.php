<?php
namespace Avetify\Components\Coding;

use Avetify\Fields\JSDatalist;
use Avetify\Forms\FormUtils;
use Avetify\Interface\CSS;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Placeable;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class CodingField extends CodingBlocks implements Placeable {
    public function __construct(public string $label, public string $mainKey,
                                string $initValue, public string $defWrapper = ""){
        parent::__construct($initValue);
        if(count($this->blocks) == 0){
            $newBlock = new CodingContentBlock([]);
            $newBlock->wrapper = $this->defWrapper;
            $this->blocks[] = $newBlock;
        }

        foreach ($this->blocks as &$block){
            $block->id = $this->mainKey . "_editor_" . hrtime(true);
        }
    }

    public function place(WebModifier $webModifier = null) {
        CodingWrappersDatalist::placeDatalist();
        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle(CSS::marginTop, "8px");
        Styler::closeAttribute();
        HTMLInterface::addAttribute("id", $this->getMainContainerId());
        HTMLInterface::closeTag();

        $labelModifier = WebModifier::createInstance();
        $labelModifier->pushStyle(CSS::fontWeight, "bold");
        $labelModifier->pushStyle(CSS::marginBottom, "2px");
        HTMLInterface::placeDiv($this->label, $labelModifier);

        foreach ($this->blocks as $block) {
            $this->addBlock($block);
        }

        HTMLInterface::closeDiv();
        $htmlSafeContents = htmlspecialchars($this->contents, ENT_QUOTES, 'UTF-8');
        FormUtils::placeHiddenField($this->getMainElementId(), $htmlSafeContents);

        $refParams =  "'{$this->getMainElementId()}', {$this->getJSDataVarName()}";
        ?>
        <script>
            const <?php echo $this->getJSDataVarName(); ?> = [];

            <?php foreach ($this->blocks as $block) {
                $blockId = $block->id;
                $blockContents = $block->contents;
                $blockDir = $block->dir;
            ?>
            <?php echo $this->getJSDataVarName(); ?>.push({
                id: '<?php echo $blockId; ?>',
                quill: defaultInitEditor(
                    '<?php echo $blockId; ?>',
                    <?php echo json_encode($blockContents); ?>,
                    '<?php echo $blockDir; ?>')
            })
            <?php } ?>

            document.addEventListener("submit", function(event) {
                refreshCodingFieldDataElement(<?php echo $refParams; ?>)
            }, true);
        </script>
        <?php
    }

    public function addBlock(CodingContentBlock $block){
        $newEditorId = $block->id;
        $wrapperId = $newEditorId . "_wrapper";
        $initWrapperValue = $block->wrapper;

        echo '<div id="' . $wrapperId . '" class="editor-wrapper avt-wrapper" style="position: relative;">';
        echo '<div ';
        HTMLInterface::addAttribute("id", $newEditorId);
        Styler::startAttribute();
        Styler::addStyle("height", "200px");
        Styler::closeAttribute();
        Styler::classStartAttribute();
        Styler::addClass("editor_block");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        HTMLInterface::closeDiv();

        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("floating-tools");
        Styler::closeAttribute();
        HTMLInterface::closeTag();

        $params = "'{$this->getMainElementId()}', '$newEditorId', {$this->getJSDataVarName()}";

        echo '<button ';
        HTMLInterface::addAttribute("onclick", "addChildAfter(" . $params . ");");
        HTMLInterface::addAttribute("type","button");
        HTMLInterface::addAttribute("id", $newEditorId . "_create");
        HTMLInterface::closeTag();
        echo '➕';
        echo '</button>';

        echo '<button ';
        HTMLInterface::addAttribute("onclick", "moveElementUp(" . $params . ");");
        HTMLInterface::addAttribute("type","button");
        HTMLInterface::addAttribute("id", $newEditorId . "_moveup");
        HTMLInterface::closeTag();
        echo '⬆️';
        echo '</button>';

        echo '<button ';
        HTMLInterface::addAttribute("onclick", "moveElementDown(" . $params . ");");
        HTMLInterface::addAttribute("type","button");
        HTMLInterface::addAttribute("id", $newEditorId . "_movedown");
        HTMLInterface::closeTag();
        echo '⬇️';
        echo '</button>';

        echo '<button ';
        HTMLInterface::addAttribute("onclick", "setPlainWrapper(" . $params . ");");
        HTMLInterface::addAttribute("type","button");
        HTMLInterface::addAttribute("id", $newEditorId . "_plain");
        HTMLInterface::closeTag();
        echo '<span style="font-weight: bold; color: #005cbf">P</span>';
        echo '</button>';

        echo '<input ';
        HTMLInterface::addAttribute("type", "text");
        HTMLInterface::addAttribute("id", $newEditorId . "_type");
        HTMLInterface::addAttribute("list", CodingWrappersDatalist::DATALIST_KEY);
        HTMLInterface::addAttribute("placeholder", "Select Wrapper");
        HTMLInterface::addAttribute("value", $initWrapperValue);
        HTMLInterface::closeSingleTag();

        HTMLInterface::closeDiv();
        HTMLInterface::closeDiv();
    }

    public function getMainElementId() : string {
        return $this->mainKey;
    }

    public function getJSDataVarName() : string {
        return $this->mainKey . "_blocks_data";
    }

    public function getMainContainerId() : string {
        return $this->mainKey . "_main_container";
    }
}
