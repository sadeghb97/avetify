<?php
namespace Avetify\Utils;

use Avetify\Interface\Platform;
use http\Exception\RuntimeException;

class CliUtils {
    /** @deprecated Use isCli() in Platform class in Interface package instead. */
    public static function isCli() : bool {
        return php_sapi_name() == "cli";
    }

    public static function readClipboardText(string $prompt = 'Enter contents'): string|false {
        if (!Platform::isCli()) {
            throw new RuntimeException(
                'You can use CliUtils::readClipboardText() only in CLI'
            );
        }

        if (!function_exists('pcntl_signal')) {
            throw new RuntimeException(
                'pcntl extension is required to support Ctrl+C cancellation.'
            );
        }

        $cancelled = false;

        pcntl_async_signals(true);

        $previousSigintHandler = pcntl_signal_get_handler(SIGINT);

        pcntl_signal(SIGINT, function () use (&$cancelled) {
            $cancelled = true;
            echo PHP_EOL . 'Input cancelled.' . PHP_EOL;
        });

        try {
            echo $prompt . PHP_EOL;
            echo 'Copy contents to your clipboard and press Enter';
            echo PHP_EOL;

            fgets(STDIN);

            if ($cancelled) {
                return false;
            }

            $commands = [
                'wl-paste --no-newline 2>/dev/null',
                'xclip -selection clipboard -o 2>/dev/null',
                'xsel --clipboard --output 2>/dev/null',
            ];

            $content = null;

            foreach ($commands as $command) {
                $output = shell_exec($command);

                if ($output !== null) {
                    $content = $output;
                    break;
                }
            }

            if ($content === null) {
                throw new RuntimeException(
                    'Failed to read clipboard. Make sure wl-paste, xclip, or xsel is installed.'
                );
            }

            echo 'Clipboard text length: ' . strlen($content) . PHP_EOL;

            return $content;
        } finally {
            pcntl_signal(SIGINT, $previousSigintHandler);
        }
    }
}
