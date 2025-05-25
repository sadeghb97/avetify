<?php
namespace Avetify\Calc;

use Avetify\Externals\JDF;

abstract class IRDateStatsCalculator extends DateStatsCalculator {
    protected function getYear(int $time): int {
        return JDF::jdate("Y", $this->adjustTime($time), '', 'Asia/Tehran', 'en');
    }

    protected function getMonth(int $time): int {
        return JDF::jdate("n", $this->adjustTime($time), '', 'Asia/Tehran', 'en');
    }
}