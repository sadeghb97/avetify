<?php

class FormUtils {
    public static function readyFormToCatchNoNamedFields(
        string $jsArgsName,
        string $formId,
        string $hiddenRawElementId,
        array $fields,
        bool $isEditable = true,
        string $hiddenSelectorElementId = null,
        array $selectFields = null
    ){
        $allJSFieldsRaw = json_encode($fields);
        ?>
            <script>
                <?php echo $jsArgsName ?>.fields = <?php echo $allJSFieldsRaw; ?>;

                <?php if($isEditable){ ?>
                document.getElementById("<?php echo $formId ?>").addEventListener("submit", function(event) {
                    const values = {}
                    <?php echo $jsArgsName ?>.fields.forEach((fieldElementId) => {
                        const fieldElement = document.getElementById(fieldElementId)
                        if(fieldElement.type === "checkbox"){
                            values[fieldElementId] = !!fieldElement.checked;
                        }
                        else values[fieldElementId] = fieldElement.value
                    })
                    const valuesRaw = JSON.stringify(values);
                    const tableFieldsRawElement = document.getElementById("<?php echo $hiddenRawElementId ?>");
                    tableFieldsRawElement.value = valuesRaw;

                    <?php if($selectFields && $hiddenSelectorElementId){
                        $allSelectFieldsRaw = json_encode($selectFields);
                    ?>
                        <?php echo $jsArgsName ?>.selectFields = <?php echo $allSelectFieldsRaw; ?>;
                        const selectValues = [];
                        <?php echo $jsArgsName ?>.selectFields.forEach((fieldElementId) => {
                            const fieldElement = document.getElementById(fieldElementId);
                            if(!!fieldElement.checked){
                                selectValues.push(fieldElementId);
                            }
                        })
                        const selectValuesRaw = JSON.stringify(selectValues);
                        const tableSelectorRawElement = document.getElementById("<?php echo $hiddenSelectorElementId ?>");
                        tableSelectorRawElement.value = selectValuesRaw;
                        console.log(selectValues)
                    <?php } ?>
                });
                <?php } ?>
            </script>
        <?php
    }

    public static function openPostForm(string $id){
        echo '<form ';
        HTMLInterface::addAttribute("method", "post");
        HTMLInterface::addAttribute("id", $id);
        HTMLInterface::addAttribute("name", $id);
        echo ' >';
    }

    public static function closeForm(){
        echo '</form>';
    }

    public static function placeSubmitButton(string $title, int $marginTop = 12,
                                             string $buttonStyle = "", WebModifier $webModifier = null){
        $joshButton = new JoshButton($title, "", $buttonStyle, "submit");
        heightMargin($marginTop);
        $joshButton->renderButton($webModifier);
    }

    public static function placeAbsSubmitButton(string $icon, string $formId, string $triggerName,
                                                int $iconSize = 60,
                                                array $position = ["right" => "20px", "bottom" => "20px"]){
        $button = new AbsoluteFormButtons($formId, $triggerName, $position, $icon);
        $button->iconSize = $iconSize;
        $button->place();
    }

    public static function placeHiddenField($id, $value, $ignoreName = false){
        echo '<input type="hidden"';
        HTMLInterface::addAttribute("id", $id);
        if(!$ignoreName) HTMLInterface::addAttribute("name", $id);
        HTMLInterface::addAttribute("value", $value);
        echo '>';
    }

    public static function placeSilentField($id, $value, $ignoreName = true){
        self::placeHiddenField($id, $value, true);
    }
}
