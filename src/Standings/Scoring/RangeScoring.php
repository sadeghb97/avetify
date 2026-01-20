<?php
namespace Avetify\Standings\Scoring;

use Avetify\Entities\EntityUtils;

class RangeScoring {
    /** @var ScoreThreshold[] */
    public array $thresholds = [];

    public function __construct(){}

    public function sortThresholds() : void {
        EntityUtils::simpleSort($this->thresholds, "value", true);
    }

    public function addThreshold(float $value, float $score) : void {
        $this->thresholds[] = new ScoreThreshold($value, $score);
        $this->sortThresholds();
    }

    public function getScore($targetValue) : float {
        if(count($this->thresholds) <= 0) return 0;
        if($targetValue < $this->thresholds[0]->value) return 0;

        for ($i=0; count($this->thresholds) > $i; $i++){
            $curValue = $this->thresholds[$i]->value;
            $curScore = $this->thresholds[$i]->score;
            if(count($this->thresholds) <= ($i + 1)) return $curScore;
            $nextValue = $this->thresholds[$i + 1]->value;
            $nextScore = $this->thresholds[$i + 1]->score;

            if($targetValue < $nextValue){
                $valueDiff = $nextValue - $curValue;
                $scoreDiff = $nextScore - $curScore;

                return $curScore + ((($targetValue - $curValue) / $valueDiff) * $scoreDiff);
            }
        }

        return $this->thresholds[count($this->thresholds) - 1]->score;
    }
}