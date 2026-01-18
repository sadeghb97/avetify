<?php
namespace Avetify\Calc;

abstract class GlobalDateStats extends DateStats {
    protected function getYear(int $time): int {
        return date("Y", $this->adjustTime($time));
    }

    protected function getMonth(int $time): int {
        return date("n", $this->adjustTime($time));
    }
}