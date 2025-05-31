<?php
namespace Avetify\Modules;

use Throwable;

class GracefulShutdown {
    private $callback;
    private $shutdownRegistered = false;

    public function __construct(callable $callback) {
        $this->callback = $callback;

        if (php_sapi_name() === 'cli') {
            if (function_exists('pcntl_signal')) {
                pcntl_async_signals(true); // PHP 7.1+
                pcntl_signal(SIGINT, [$this, 'handleSignal']);  // Ctrl+C
                pcntl_signal(SIGTERM, [$this, 'handleSignal']); // kill
            }
        }

        $this->registerShutdown();
    }

    public function handleSignal($signo) {
        $this->runCallback("Signal received: $signo");
        exit(1);
    }

    public function registerShutdown() {
        if (!$this->shutdownRegistered) {
            register_shutdown_function([$this, 'handleShutdown']);
            $this->shutdownRegistered = true;
        }
    }

    public function handleShutdown() {
        if (connection_aborted() || error_get_last()) {
            $this->runCallback("Shutdown triggered");
        }
        else if(php_sapi_name() === 'cli'){
            $this->runCallback("done");
        }
    }

    private function runCallback($reason = '') {
        if ($this->callback) {
            try {
                call_user_func($this->callback, $reason);
            } catch (Throwable $e) {
                fwrite(STDERR, "Graceful shutdown error: {$e->getMessage()}\n");
            }
            $this->callback = null;
        }
    }
}

