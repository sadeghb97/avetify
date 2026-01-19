<?php
namespace Avetify\Standings\Calc;

use Avetify\Standings\Models\DateStatItem;

abstract class DateStats {
    public array $yearStats = [];

    protected function adjustTime(int $time) : int {
        return $time;
    }

    protected abstract function getYear(int $time) : int;
    protected abstract function getMonth(int $time) : int;

    protected abstract function getEmptyStatObject(int $year, int $month = 0) : DateStatItem;

    public function pushRecord($record, $time) : void {
        if($time < 1000) return;
        $year = (int) trim($this->getYear($this->adjustTime($time)));
        $month = (int) trim($this->getMonth($this->adjustTime($time)));

        if(!isset($this->yearStats[$year])){
            $this->yearStats[$year] = [];
            $this->yearStats[$year][0] = $this->getEmptyStatObject($year);
            for($i = 1; 12 >= $i; $i++){
                $this->yearStats[$year][$i] = $this->getEmptyStatObject($year, $i);
            }
        }

        $yearStatObject = $this->yearStats[$year][0];
        $monthStatObject = $this->yearStats[$year][$month];

        $yearStatObject->applyRecord($record);
        $monthStatObject->applyRecord($record);
    }

    public function sortStats(string $sortFactor = "overallScore") : void {
        ksort($this->yearStats);
        foreach ($this->yearStats as $ys){
            for ($i = 0; 12>=$i; $i++){
                $ys->sortCandidates($sortFactor);
            }
        }
    }

    public function getAllYearStats() : array {
        $stats = [];
        foreach ($this->yearStats as $st){
            $stats[] = $st[0];
        }
        return $stats;
    }

    public function getAllMonthStats() : array {
        $stats = [];
        foreach ($this->yearStats as $st){
            foreach ($st as $month => $monthStat){
                if($month < 1) continue;
                $stats[] = $monthStat;
            }
        }
        return $stats;
    }
}
