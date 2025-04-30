<?php

class LeagueStandings {
    /** @var Competitor[] $competitors */
    public array $competitors;

    public function __construct(array $competitors){
        $this->competitors = $competitors;
    }

    public static function importStyles(){
        ThemesManager::importStyle(Routing::getAvetifyRoot() . "standings/standings.css");
        echo '<link href="https://fonts.googleapis.com/css?family=Fjalla+One" rel="stylesheet">';
    }

    public function sort(){
        usort($this->competitors, [$this, 'mainSort']);
    }

    public function renderTable($title = null){
        echo '<div class="ptable">';
        if($title != null) echo '<h1 class="headin">' . $title . '</h1>';
        echo '<table>';

        echo '<tr class="col">
                <th>#</th>
                <th>Competitor</th>
                <th>GP</th>
                <th>W</th>
                <th>D</th>
                <th>L</th>
                <th>SC</th>
                <th>GD</th>
                <th>PTS</th>
                </tr>';

        $this->sort();

        foreach ($this->competitors as $competitorIndex => $competitor){
            $competitorRank = $competitorIndex + 1;
            echo '<tr class="col">';
            echo '<th>' . $competitorRank . '</th>';
            echo '<th>' . $competitor->name . '</th>';
            echo '<th>' . $competitor->gp . '</th>';
            echo '<th>' . $competitor->w . '</th>';
            echo '<th>' . $competitor->d . '</th>';
            echo '<th>' . $competitor->l . '</th>';
            echo '<th>' . $competitor->scores . '</th>';
            echo '<th>' . $competitor->scores - $competitor->conceded . '</th>';
            echo '<th>' . $competitor->points . '</th>';
            echo '</tr>';
        }

        echo '</table></div>';
    }

    private function mainSort(Competitor $a, Competitor $b) : int {
        if($a->points > $b->points) return -1;
        else if($b->points > $a->points) return 1;

        else if(($a->scores - $a->conceded) > ($b->scores - $b->conceded)) return -1;
        else if(($b->scores - $b->conceded) > ($a->scores - $a->conceded)) return 1;

        else if($a->scores > $b->scores) return -1;
        else if($b->scores > $a->scores) return 1;

        return 0;
    }
}
