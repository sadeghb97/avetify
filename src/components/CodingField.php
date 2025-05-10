<?php

class CodingField implements Placeable {
    public array $sectionIds = [];

    public function __construct(public string $label, public string $mainKey, public string $initValue){}

    public function place(WebModifier $webModifier = null) {
        CodingWrappersDatalist::placeDatalist();
        echo '<div ';
        HTMLInterface::addAttribute("id", $this->getMainContainerId());
        HTMLInterface::closeTag();
        $this->addBlock();
        HTMLInterface::closeDiv();
        FormUtils::placeHiddenField($this->getMainElementId(), $this->initValue);

        $refParams =  "'{$this->getMainElementId()}', {$this->getJSDataVarName()}";
        ?>
        <script>
            const <?php echo $this->getJSDataVarName(); ?> = [];

            <?php foreach ($this->sectionIds as $sectionId) { ?>
                <?php echo $this->getJSDataVarName(); ?>.push({
                id: '<?php echo $sectionId; ?>',
                quill: defaultInitEditor('<?php echo $sectionId; ?>')
            })
            <?php } ?>

            document.addEventListener("submit", function(event) {
                refreshCodingFieldDataElement(<?php echo $refParams; ?>)
            }, true);
        </script>
        <?php

        /*echo '<textarea id="tmp_result" rows="15", cols="80"></textarea>';

        echo '<button ';
        HTMLInterface::addAttribute("type", "text");
        HTMLInterface::addAttribute("onclick", "refreshCodingFieldDataElement(" . $refParams . ");");
        HTMLInterface::closeTag();
        echo '➕';
        echo '</button>';*/
    }

    public function addBlock(){
        $newEditorId = $this->mainKey . "_editor_" . hrtime(true);
        $this->sectionIds[] = $newEditorId;
        $wrapperId = $newEditorId . "_wrapper";

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

        echo '<input ';
        HTMLInterface::addAttribute("type", "text");
        HTMLInterface::addAttribute("id", $newEditorId . "_type");
        HTMLInterface::addAttribute("list", CodingWrappersDatalist::DATALIST_KEY);
        HTMLInterface::addAttribute("placeholder", "Select Wrapper");
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
        $records = [
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

        parent::__construct(self::DATALIST_KEY, $records, "slug", "title");
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
