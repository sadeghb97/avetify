<?php

class FormUtils {
    public static function readyFormToCatchNoNamedFields(
        string $jsArgsName,
        string $formId,
        string $hiddenRawElementId,
        string $allJSFieldsRaw,
        bool $isEditable = true,
        string $hiddenSelectorElementId = null,
        array $selectFields = null
    ){
        ?>
            <script>
                <?php echo $jsArgsName ?>.fields = <?php echo $allJSFieldsRaw; ?>;

                <?php if($isEditable){ ?>
                document.getElementById("<?php echo $formId; ?>").addEventListener("submit", function(event) {
                    const values = {}
                    <?php echo $jsArgsName ?>.fields.forEach((fieldElementId) => {
                        const fieldElement = document.getElementById(fieldElementId)
                        if(fieldElement) {
                            if (fieldElement.type === "checkbox") {
                                values[fieldElementId] = !!fieldElement.checked;
                            } else values[fieldElementId] = fieldElement.value
                        }
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
                    <?php } ?>
                });
                <?php } ?>
            </script>
        <?php
    }

    public static function openPostForm(string $id, WebModifier|null $webModifier = null){
        echo '<form ';
        HTMLInterface::addAttribute("method", "post");
        HTMLInterface::addAttribute("id", $id);
        HTMLInterface::applyModifiers($webModifier);
        Styler::startAttribute();
        HTMLInterface::appendStyles($webModifier);
        Styler::closeAttribute();
        echo ' >';
    }

    public static function closeForm(){
        echo '</form>';
    }

    public static function placeSubmitButton(string $title, string $buttonStyle = "",
                                             int $marginTop = 12, WebModifier $webModifier = null){
        $joshButton = new JoshButton($title, "", $buttonStyle, "submit");
        HTMLInterface::placeVerticalDivider($marginTop);
        $joshButton->renderButton($webModifier);
    }

    public static function placeAbsSubmitButton(string $icon, string $formId, string $triggerName,
                                                int $iconSize = 60,
                                                array $position = ["inset-inline-end" => "20px", "bottom" => "20px"]){
        $button = new AbsoluteFormButton($formId, $triggerName, $position, $icon);
        $button->iconSize = $iconSize;
        $button->place();
    }

    public static function fastSubmitButton(string $icon){
        $mainFormId = "main_form";
        self::openPostForm($mainFormId);
        $position = ["inset-inline-end" => "20px", "bottom" => "20px"];

        $button = new AbsoluteFormButton($mainFormId, "default", $position, $icon);
        $button->iconSize = 60;
        $button->place();

        self::placeHiddenField("main_form_data");
        self::closeForm();
    }

    public static function placeLinkButton(string $image, string $url, WebModifier $webModifier){
        $linkButton = new IconButton($image, 60, "redir('" . $url . "')");
        $linkButton->place($webModifier);
    }

    public static function placeHiddenField($id, $value = "", $ignoreName = false){
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
