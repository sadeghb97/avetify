<?php
abstract class RecordContextMenu {
    public int $singleWidth = 115;
    public int $singleHeight = 35;
    public string | null $currentRecord = null;

    public function __construct(public array $options, public string $ctxId, public int $rowLength = 2){
    }

    public function placeMenu(){
        echo '<div ';
        HTMLInterface::addAttribute("id", $this->menuIdentifier());
        HTMLInterface::addAttribute("class", "context-menu-nice");
        echo '<div id="context-menu" class="context-menu-nice" ';
        Styler::startAttribute();
        Styler::addStyle("width", ($this->singleWidth * $this->rowLength) . "px");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo '<div class="context-menu-row">';

        foreach ($this->options as $rowOptions){
            if($rowOptions instanceof ContextMenuItem) {
                $this->addMenuItem($rowOptions, $this->singleWidth * $this->rowLength);
            }
            else {
                foreach ($rowOptions as $index => $option) {
                    $width = $this->singleWidth;
                    if((($index + 1) >= count($rowOptions)) && (count($rowOptions) < $this->rowLength)){
                        $width = ($this->rowLength - $index) * $this->singleWidth;
                    }
                    $this->addMenuItem($option, $width);
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
                const menuWidth = <?php echo $this->rowLength * $this->singleWidth ?>;
                const menuHeight = <?php echo count($this->options) * $this->singleHeight ?>;
                const screenWidth = window.innerWidth;
                const screenHeight = window.innerHeight;
                const xMenu = ((event.clientX + menuWidth) > screenWidth) ?
                    screenWidth - menuWidth : event.clientX;
                const yMenu = ((event.clientY + menuHeight) > screenHeight) ?
                    screenHeight - menuHeight : event.clientY;

                <?php echo $this->currentRecordVarName(); ?> = recordId;
                <?php echo $this->menuVarName(); ?>.style.top = yMenu + "px";
                <?php echo $this->menuVarName(); ?>.style.left = xMenu + "px";

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

    public static function makeContextMenuOptions(array $singleDimOptions, int $rowLength) : array {
        $options = [];
        $rem = (count($singleDimOptions) % $rowLength);

        $firstRow = [];
        for($i=0; $rem > $i; $i++){
            $firstRow[] = $singleDimOptions[$i];
        }
        if(count($firstRow) > 0) $options[] = $firstRow;

        $remainedTags = $rem == 0 ? $singleDimOptions : array_slice($singleDimOptions, $rem);

        for ($i=0; count($remainedTags) > ($rowLength * $i); $i++){
            $rowOptions = [];
            for($j=0; $rowLength > $j; $j++){
                $rowOptions[] = $remainedTags[$i * $rowLength + $j];
            }
            $options[] = $rowOptions;
        }
        return $options;
    }

    protected function addMenuItem(ContextMenuItem $menuItem, int $width){
        echo '<div ';
        HTMLInterface::addAttribute("class", "item");
        Styler::startAttribute();
        Styler::addStyle("width", $width . "px");
        Styler::addStyle("height", $this->singleHeight . "px");
        HTMLInterface::appendStyles($menuItem->modifier);
        Styler::closeAttribute();
        Styler::classStartAttribute();
        HTMLInterface::appendClasses($menuItem->modifier);
        Styler::closeAttribute();
        HTMLInterface::addAttribute("onclick",
            $this->provokerActionName() . "('" . $menuItem->key . "')");
        HTMLInterface::applyModifiers($menuItem->modifier);
        HTMLInterface::closeTag();
        echo $menuItem->title;
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

class ContextMenuItem {
    public function __construct(public string $key, public string $title){}
    public ?WebModifier $modifier;
}