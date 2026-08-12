<?php
namespace Avetify\Interface;

class Cookies {
    public static function avtKey(string $key) : string {
        return "avt_" . $key;
    }

    public static function getCookieValue(string $key): ?string {
        $finalKey = self::avtKey($key);

        if (!isset($_COOKIE[$finalKey])) {
            return null;
        }

        return rawurldecode($_COOKIE[$finalKey]);
    }

    public static function setCookieValue(string $key, string $value): void {
        $finalKey = self::avtKey($key);
        ?>
        <script>
          document.cookie =
              <?= json_encode($finalKey . '=' . rawurlencode($value) . '; path=/') ?>;
        </script>
        <?php
    }

    public static function removeCookieValue(string $key): void {
        $finalKey = self::avtKey($key);
        ?>
        <script>
          document.cookie =
              <?= json_encode($finalKey . '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/') ?>;
        </script>
        <?php
    }
}
