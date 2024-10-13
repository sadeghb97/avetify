<?php

class FormUtils {
    public static function readyFormToCatchNoNamedFields(
        string $jsArgsName,
        string $formId,
        string $hiddenRawElementId,
        array $fields,
        bool $isEditable = true
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
                        values[fieldElementId] = fieldElement.value
                    })
                    const valuesRaw = JSON.stringify(values)
                    const tableFieldsRawElement = document.getElementById("<?php echo $hiddenRawElementId ?>")
                    tableFieldsRawElement.value = valuesRaw
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

    public static function placeSubmitButton($title, $marginTop = "6px"){
        echo '<button type="submit" class="btn btn-primary" ';
        Styler::startAttribute();
        Styler::addStyle("margin-top", $marginTop);
        Styler::closeAttribute();
        echo ' >';
        echo $title;
        echo '</button>';
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
