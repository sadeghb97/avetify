<?php

class JSInterface {
    public static function declareGlobalJSArgs($argsName){
        ?>
        <script>
            const <?php echo $argsName; ?> = {}
        </script>
        <?php
    }

    public static function log(string $message, string $tag = ""){
        $finalMessage = $tag ? ($tag . ": " . $message) : $message;
        ?>
        <script>
            console.log("<?php echo $finalMessage; ?>")
        </script>
        <?php
    }

    public static function redirect(string $url, $delay = 0){
        ?>
        <script>
            redir("<?php echo $url; ?>", <?php echo $delay; ?>)
        </script>
        <?php
    }
}
