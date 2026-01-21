<?php
namespace Avetify\Standings\Calc;

abstract class GlobalDateStats extends DateStats {
    public function getYear(int $time): int {
        return date("Y", $this->adjustTime($time));
    }

    public function getMonth(int $time): int {
        return date("n", $this->adjustTime($time));
    }
}