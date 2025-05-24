<?php
namespace Avetify\Modules;

use function Avetify\Utils\br;

class SecureNetwork {
    public int $consFails = 0;

    public function __construct(public int $maxConsFails = 2, public int $delay = 30){
    }

    public function success(){
        $this->consFails = 0;
    }

    public function fail($message = "") {
        $this->consFails++;
        echo ($message ? ($message . ': ') : "");

        if(!$this->reachMaxConsFails()) {
            echo $this->consFails . '/' . $this->maxConsFails . " -> "
                . 'Sleep: ' . $this->delay . 's' . br();
            sleep($this->delay);
            return false;
        }
        else {
            echo "Max consecutive fails reached" . br();
            return true;
        }
    }

    public function reachMaxConsFails() : bool {
        return $this->consFails > $this->maxConsFails;
    }
}
