<?php
abstract class RecordContextMenu {
    public int $singleWidth = 115;
    public string | null $currentRecord = null;

    public function __construct(public array $options, public string $ctxId){
    }

    public function placeMenu(){
        echo '<div ';
        HTMLInterface::addAttribute("id", $this->menuIdentifier());
        HTMLInterface::addAttribute("class", "context-menu-nice");
        echo '<div id="context-menu" class="context-menu-nice" ';
        Styler::startAttribute();
        Styler::addStyle("width", ($this->singleWidth * 2) . "px");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo '<div class="context-menu-row">';

        foreach ($this->options as $rowOptions){
            if(!empty($rowOptions['key'])) {
                $this->addMenuItem($rowOptions['key'], $rowOptions['title'], $this->singleWidth * 2);
            }
            else {
                foreach ($rowOptions as $option) {
                    $this->addMenuItem($option['key'], $option['title'], $this->singleWidth);
                }
            }
        }

        echo '</div>';
        echo '</div>';

        ?>
        <script>
            function <?php echo $this->provokerActionName(); ?>(actionName){
                <?php echo $this->actionExecutorName(); ?>(actionName, <?php echo $this->currentRecordVarName(); ?>);
            }

            function <?php echo $this->openMenuName(); ?>(recordId, event){
                if (event.target.closest('.contextmenu-exception')) {
                    // Allow the default context menu for the exception
                    return true;
                }

                event.preventDefault();
                <?php echo $this->currentRecordVarName(); ?> = recordId;
                <?php echo $this->menuVarName(); ?>.style.top = event.clientY + "px";
                <?php echo $this->menuVarName(); ?>.style.left = event.clientX + "px";

                <?php echo $this->menuVarName(); ?>.classList.remove("visible");
                setTimeout(() => {
                    <?php echo $this->menuVarName(); ?>.classList.add("visible");
                });
            }

            let <?php echo $this->currentRecordVarName(); ?> = null;
            let <?php echo $this->menuVarName(); ?> = document.getElementById('<?php echo $this->menuIdentifier(); ?>');

            document.addEventListener("click", () => {
                <?php echo $this->menuVarName(); ?>.classList.remove("visible");
            });
        </script>
        <?php

        $this->addExecutor();
    }

    protected function addMenuItem(string $key, string $title, int $width){
        echo '<div ';
        HTMLInterface::addAttribute("class", "item");
        Styler::startAttribute();
        Styler::addStyle("width", $width . "px");
        Styler::closeAttribute();
        HTMLInterface::addAttribute("onclick",
            $this->provokerActionName() . "('" . $key . "')");
        HTMLInterface::closeTag();
        echo $title;
        HTMLInterface::closeDiv();
    }

    public abstract function addExecutor() : void;

    public function openMenuName() : string {
        return "open_menu__" . $this->ctxId;
    }

    public function provokerActionName() : string {
        return "provoker__" . $this->ctxId;
    }

    public function currentRecordVarName() : string {
        return "curr__" . $this->ctxId;
    }

    public function menuVarName() : string {
        return "menu_element__" . $this->ctxId;
    }

    public function actionExecutorName() : string {
        return "executor__" . $this->ctxId;
    }

    public function menuIdentifier() : string {
        return "menu_id__" . $this->ctxId;
    }

    public function openMenuJSCall(string $recordId) : string {
        return $this->openMenuName() . "('" . $recordId . "', event)";
    }
}