<?php

class CodingField extends CodingBlocks implements Placeable {
    public function __construct(public string $label, public string $mainKey,
                                string $initValue, public string $defWrapper = ""){
        parent::__construct($initValue);
        if(count($this->blocks) == 0){
            $newBlock = new CodingContentBlock([]);
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
                $safeContents = trim(json_encode($blockContents), '"');
            ?>
            <?php echo $this->getJSDataVarName(); ?>.push({
                id: '<?php echo $blockId; ?>',
                quill: defaultInitEditor('<?php echo $blockId; ?>', '<?php echo $safeContents; ?>')
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
        $initWrapperValue = $this->defWrapper;
        if($block->wrapper) $initWrapperValue = $block->wrapper;

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

class CodingWrappersDatalist extends JSDatalist {
    protected static bool $isPlaced = false;
    public const DATALIST_KEY = "coding_wrappers_datalist";

    public function __construct(){
        $records = self::wrappersList();
        parent::__construct(self::DATALIST_KEY, $records, "slug", "title");
    }

    public static function wrappersList() : array {
        return [
            ["slug" => "plain", "title" => "Plain"],
            ["slug" => "bash", "title" => "Bash"],
            ["slug" => "c", "title" => "C"],
            ["slug" => "cpp", "title" => "CPP"],
            ["slug" => "csharp", "title" => "CSharp"],
            ["slug" => "css", "title" => "CSS"],
            ["slug" => "django", "title" => "Django"],
            ["slug" => "gradle", "title" => "Gradle"],
            ["slug" => "java", "title" => "Java"],
            ["slug" => "javascript", "title" => "JavaScript"],
            ["slug" => "json", "title" => "JSON"],
            ["slug" => "kotlin", "title" => "Kotlin"],
            ["slug" => "php", "title" => "PHP"],
            ["slug" => "php-template", "title" => "PHP-Template"],
            ["slug" => "python", "title" => "Python"],
            ["slug" => "python-repl", "title" => "Python-repl"],
            ["slug" => "scss", "title" => "SCSS"],
            ["slug" => "shell", "title" => "Shell"],
            ["slug" => "sql", "title" => "SQL"],
            ["slug" => "typescript", "title" => "TypeScript"],
            ["slug" => "xml", "title" => "XML"],
            ["slug" => "yaml", "title" => "YAML"],
            ["slug" => "output", "title" => "Output"]
        ];
    }

    public static function placeDatalist() : ?JSDatalist {
        if(!self::$isPlaced) {
            $dl = new CodingWrappersDatalist();
            $dl->place();
            self::$isPlaced = true;
            return $dl;
        }
        return null;
    }
}
