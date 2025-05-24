<?php
namespace Avetify\Calc;

use function Avetify\Externals\jdate;

abstract class IRDateStatsCalculator extends DateStatsCalculator {
    protected function getYear(int $time): int {
        return jdate("Y", $this->adjustTime($time), '', 'Asia/Tehran', 'en');
    }

    protected function getMonth(int $time): int {
        return jdate("n", $this->adjustTime($time), '', 'Asia/Tehran', 'en');
    }
}