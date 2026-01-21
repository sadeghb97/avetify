<?php
namespace Avetify\Standings\Calc;

use Avetify\Standings\Models\DateStatItem;

abstract class DateStats {
    public function __construct(public bool $logging = false){}

    public array $yearStats = [];
    public DateStatItem | null $overallStats = null;

    protected function adjustTime(int $time) : int {
        return $time;
    }

    public abstract function getYear(int $time) : int;
    public abstract function getMonth(int $time) : int;

    protected abstract function getEmptyStatObject(int $year, int $month = 0) : DateStatItem;

    public function pushRecord($record, $time) : void {
        if($time < 1000) return;
        $year = (int) trim($this->getYear($this->adjustTime($time)));
        $month = (int) trim($this->getMonth($this->adjustTime($time)));

        if($this->overallStats == null){
            $this->overallStats = $this->getEmptyStatObject(0);
        }
        if(!isset($this->yearStats[$year])){
            $this->yearStats[$year] = [];
            $this->yearStats[$year][0] = $this->getEmptyStatObject($year);
            for($i = 1; 12 >= $i; $i++){
                $this->yearStats[$year][$i] = $this->getEmptyStatObject($year, $i);
            }
        }

        $yearStatObject = $this->yearStats[$year][0];
        $monthStatObject = $this->yearStats[$year][$month];

        $this->overallStats->applyRecord($record, $this->logging);
        $yearStatObject->applyRecord($record, $this->logging);
        $monthStatObject->applyRecord($record, $this->logging);
    }

    public function sortStats(string $sortFactor = "overallScore") : void {
        ksort($this->yearStats);
        foreach ($this->yearStats as $ys){
            for ($i = 0; 12>=$i; $i++){
                $ys[$i]->sortCandidates($sortFactor);
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
