<?php
namespace Avetify\Modules\Cli;

use Avetify\Interface\Platform;

class Terminal {
    private static ?string $initialStty = null;
    private static bool $modified = false;
    private static bool $signalHandling = false;

    public static function disableUserInput(): void {
        if (!Platform::isCli()) return;

        if (!self::$modified) {
            $output = @shell_exec('stty -g');
            if ($output !== null) {
                self::$initialStty = trim($output);
            }
            register_shutdown_function([self::class, 'restore']);
            self::setupSignalHandlers();
            self::$modified = true;
        }

        @shell_exec('stty -echo -icanon');
        echo "\033[?25l";
    }

    public static function enableUserInput(): void {
        self::restore();
    }

    public static function restore(): void {
        if (!Platform::isCli()) return;

        if (self::$initialStty !== null) {
            @shell_exec('stty ' . escapeshellarg(self::$initialStty));
        }

        echo "\033[?25h";
    }

    private static function setupSignalHandlers(): void {
        if (self::$signalHandling) return;
        self::$signalHandling = true;

        pcntl_async_signals(true);
        pcntl_signal(SIGINT, function () {
            self::restore();
            exit(130); // استاندارد برای Ctrl+C
        });
        pcntl_signal(SIGTERM, function () {
            self::restore();
            exit(143); // استاندارد برای SIGTERM
        });
    }
}

