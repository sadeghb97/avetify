<?php
namespace Avetify\Modules;

use Avetify\Interface\Pout;

class TimeLogger {
    public int $lastTime = 0;

    public function __construct() {
        $this->resetTime();
    }

    public function log(string $message = ""){
        $doneTime = (int) (microtime(true) * 1000);
        $duration = $doneTime - $this->lastTime;
        $this->lastTime = $doneTime;
        $formattedDuration = self::getFormattedDuration($duration);
        echo $message . " ({$formattedDuration})" . Pout::br();
    }

    private static function getFormattedDuration(int $durationMs) : string {
        if($durationMs < 1000) return $durationMs . "ms";
        return ($durationMs / 1000) . "s";
    }

    public function resetTime(){
        $this->lastTime = (int) (microtime(true) * 1000);
    }
}
