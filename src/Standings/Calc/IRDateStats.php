<?php
namespace Avetify\Standings\Calc;

use Avetify\Externals\JDF;

abstract class IRDateStats extends DateStats {
    public function getYear(int $time): int {
        return JDF::jdate("Y", $this->adjustTime($time), '', 'Asia/Tehran', 'en');
    }

    public function getMonth(int $time): int {
        return JDF::jdate("n", $this->adjustTime($time), '', 'Asia/Tehran', 'en');
    }
}