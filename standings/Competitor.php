<?php

class Competitor {
    public function __construct(public string $name, public string $id, public int $gp = 0, public int $w = 0,
    public int $d = 0, public int $l = 0, public int $scores = 0, public int $conceded = 0, public int $points = 0){}

    public function pushResult($scores, $conceded){
        $this->gp++;
        $this->scores += $scores;
        $this->conceded += $conceded;

        if($scores > $conceded){
            $this->w++;
            $this->points += 3;
        }
        else if($conceded > $scores){
            $this->l++;
        }
        else {
            $this->d++;
            $this->points += 1;
        }
    }
}
