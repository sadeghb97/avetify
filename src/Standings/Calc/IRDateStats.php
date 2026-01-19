<?php
namespace Avetify\Standings\Calc;

use Avetify\Externals\JDF;

abstract class IRDateStats extends DateStats {
    protected function getYear(int $time): int {
        return JDF::jdate("Y", $this->adjustTime($time), '', 'Asia/Tehran', 'en');
    }

    protected function getMonth(int $time): int {
        return JDF::jdate("n", $this->adjustTime($time), '', 'Asia/Tehran', 'en');
    }
}