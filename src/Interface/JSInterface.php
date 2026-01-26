<?php
namespace Avetify\Interface;

use Avetify\Routing\Routing;

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
            redir("<?php echo html_entity_decode($url); ?>", <?php echo $delay; ?>)
        </script>
        <?php
    }

    public static function refresh($delay = 0){
        $currentUrl = Routing::getCurrentLink()
        ?>
        <script>
            redir("<?php echo html_entity_decode($currentUrl); ?>", <?php echo $delay; ?>)
        </script>
        <?php
    }

    public static function urlInNewTab(string $url){
        ?>
        <script>
            window.open('<?php echo html_entity_decode($url); ?>', '_blank');
        </script>
        <?php
    }

    public static function setLocalStorageValue(string $key, string $value) : void {
        ?>
        <script>
            localStorage.setItem(
                    <?php echo json_encode($key); ?>,
                    <?php echo json_encode($value); ?>
            );
        </script>
        <?php
    }

    public static function removeLocalStorageValue(string $key) : void {
        ?>
        <script>
            localStorage.removeItem(<?php echo json_encode($key); ?>);
        </script>
        <?php
    }
}
