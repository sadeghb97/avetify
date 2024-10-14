<?php
abstract class RankingList {
    public array $allItems = [];
    public string $width;
    public int $baseSize;
    public string $title;

    public function __construct($items, $title, $width, $baseSize){
        $this->allItems = $items;
        $this->title = $title;
        $this->width = $width;
        $this->baseSize = $baseSize;
    }

    abstract public function getItemImage($item) : string;

    abstract public function getItemTitle($item) : string;

    public function getItemScore($item) : ?int {
        return null;
    }

    public function getColorPalettes(){
        return [
            "#FFF455", "#80C4E9", "#50B498", "#E7F0DC"
        ];
    }

    public function renderRanking(){
        $this->printTop20Section($this->allItems, $this->title, $this->baseSize, $this->width);
    }

    public static function addStyles(){
        echo '<style>
            .babegrid {
                border: 1px;
                border-style: solid;
            }
        </style>';
    }

    public function printTop20Section($items, $title, $baseSize, $width){
        $colors = $this->getColorPalettes();
        $primaryBG = $colors[0];
        $secondaryBG = $colors[1];
        $ternaryBG = $colors[2];
        $alterBG = $colors[3];

        $rank = 1;
        echo '<div style="width: ' . $width . '; margin: auto; border: 2px solid;">
        <div style="width: 100%; display: grid; padding-top: 4px; padding-bottom: 4px; text-align: center" class="babegrid">';
        echo $title;
        echo '</div>
        
	    <div style="width: 100%; display: flex;">
            <div style="width: 50%; height: ' . $baseSize * 50 . 'px; background-color: ' . $primaryBG . ';" class="babegrid">';
        $this->printBabeGrid($items[0], $rank++, $baseSize * 5);
        echo '</div>
		
		    <div style="width: 50%; height: ' . $baseSize * 50 . 'px; background-color: ' . $secondaryBG . ';">
			    <div style="width: 100%; height: 50%; display: flex;">
				    <div style="width: 50%; height: 100%;" class="babegrid">';
        $this->printBabeGrid($items[1], $rank++, $baseSize * 2.5);
        echo '</div>
				
				    <div style="width: 50%; height: 100%;" class="babegrid">';
        $this->printBabeGrid($items[2], $rank++, $baseSize * 2.5);
        echo '</div>
			    </div>
			
			    <div style="width: 100%; height: 50%; display: flex;">
				    <div style="width: 50%; height: 100%;" class="babegrid">';
        $this->printBabeGrid($items[3], $rank++, $baseSize * 2.5);
        echo '</div>
				
				<div style="width: 50%; height: 100%;" class="babegrid">';
        $this->printBabeGrid($items[4], $rank++, $baseSize * 2.5);
        echo '</div>
			</div>
		</div>
	</div>

	<div style="width: 100%; display: flex; background-color: ' . $ternaryBG . ';">';
        for($i = 5; 10>$i; $i++){
            echo '<div style="width: 20%; height: ' . $baseSize * 20 . 'px;" class="babegrid">';
            $this->printBabeGrid($items[$i], $rank++, $baseSize * 2);
            echo '</div>';
        }
        echo '</div>
	<div style="width: 100%; display: flex; background-color: ' . $alterBG . ';">';
        for($i = 10; 20>$i; $i++){
            echo '<div style="width: 10%; height: ' . $baseSize * 10 . 'px;" class="babegrid">';
            $this->printBabeGrid($items[$i], $rank++, $baseSize);
            echo '</div>';
        }
        echo '</div>
        </div>';

        echo '<div style="height: 36px;"></div>';
    }

    function printBabeGrid($item, $rank, $size){
        $avatar = $this->getItemImage($item);
        $title = $this->getItemTitle($item);
        $score = $this->getItemScore($item);
        $fontSize = (int)($size * 0.75);

        echo '<div style="width: 100%; height: 90%;">
            <img src="' . $avatar .
            '" title="' . $title . '" style="height: 100%; width: auto; margin: auto; display: block;" />
        </div>';

        echo '<div style="width: 100%; height: 10%; display: flex">
            <span style="margin: auto; display: block; font-size: ' . $fontSize . 'px;">';
        echo $rank . ': ' . $title . ($score ? (' ('. formatMegaNumber($score) . ')') : '');
        echo '</span>
        </div>';
    }
}