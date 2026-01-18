<?php
namespace Avetify\Calc;

abstract class DateStats {
    /** @var array[] */
    public array $yearStats = [];

    protected function adjustTime(int $time) : int {
        return $time;
    }

    protected abstract function getYear(int $time) : int;
    protected abstract function getMonth(int $time) : int;
    protected abstract function getEmptyStatObject(int $year, int $month = 0);
    protected abstract function adjustStat($stat, $record);

    public function pushRecord($record, $time){
        if($time < 1000) return;
        $year = (int) trim($this->getYear($time));
        $month = (int) trim($this->getMonth($time));
        $this->_pushRecord($record, $year, $month);
    }

    public function sortStats(){
        ksort($this->yearStats);
    }

    protected function _pushRecord($record, int $year, int $month){
        if(!isset($this->yearStats[$year])){
            $this->yearStats[$year] = [];
            $this->yearStats[$year][0] = $this->getEmptyStatObject($year);
            for($i = 1; 12 >= $i; $i++){
                $this->yearStats[$year][$i] = $this->getEmptyStatObject($year, $i);
            }
        }

        $yearStatObject = $this->yearStats[$year][0];
        $monthStatObject = $this->yearStats[$year][$month];

        $this->adjustStat($yearStatObject, $record);
        $this->adjustStat($monthStatObject, $record);
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
