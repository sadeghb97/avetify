<?php
namespace Avetify\Modules\Cli;

use Avetify\Interface\Pout;
use Avetify\Interface\Platform;

class CliPrinter {
    private bool $colorSupported;

    public function __construct() {
        $this->colorSupported = Platform::isCli() && $this->detectColorSupport();
    }

    private function detectColorSupport(): bool {
        if (!function_exists('posix_isatty')) {
            return false;
        }

        $isTTY = posix_isatty(STDOUT);
        if (stripos(PHP_OS_FAMILY, 'Windows') !== false) {
            $version = php_uname('r');
            return $isTTY && version_compare($version, '10.0.10586', '>=');
        }

        return $isTTY;
    }

    private function _print(string $text, ?string $color = null, bool $newline = true): void {
        if ($this->colorSupported && $color !== null) {
            echo "\033[{$color}m{$text}\033[0m";
        }
        else echo $text;
        if ($newline) Pout::endline();
    }

    public function print(string $text, ?string $color = null): void {
        $this->_print($text, $color, false);
    }

    public function println(string $text, ?string $color = null): void {
        $this->_print($text, $color, true);
    }
}
