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
        echo $message . " ({$duration}ms)" . Pout::br();
    }

    public function resetTime(){
        $this->lastTime = (int) (microtime(true) * 1000);
    }
}
