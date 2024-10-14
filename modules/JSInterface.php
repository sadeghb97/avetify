<?php

class JSInterface {
    public static function declareGlobalJSArgs($argsName){
        ?>
        <script>
            const <?php echo $argsName; ?> = {}
        </script>
        <?php
    }

    public static function addAbsoluteIconButton(string $imageSrc, array $positionStyles, string $rawOnclick = ""){
        echo '<div style=" ';
        Styler::addStyle("position", "fixed");
        Styler::addStyle("cursor", "pointer");
        foreach ($positionStyles as $psKey => $psValue){
            Styler::addStyle($psKey, $psValue);
        }
        Styler::closeAttribute();
        HTMLInterface::addAttribute("class", "img-button");
        if($rawOnclick) HTMLInterface::addAttribute("onclick", $rawOnclick);
        echo ' >';
        echo '<img src="' . $imageSrc . '" alt="Icon" style="width: 50px; height: 50px; border-radius: 50%;">';
        echo '</div>';
    }
}
