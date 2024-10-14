<?php

function printCard($img, $name, $description, $link, $options){
    global $currentTime;
    echo '<div class="card">';

    if($img) {
        echo '<div class="card__header">
        <img src="' . $img . '" alt="card__image" class="card__image" width="600">
        </div>';
    }

    echo '<div class="card__body">';
        if(isset($options['magham'])){
            echo '<div style="margin-top: 8px; font-weight: bold; font-size: 12px; color: firebrick">';
            echo $options['magham'];
            echo '</div>';
        }
        if(isset($options['tag'])) echo '<span class="tag tag-blue">' . $options['tag'] . '</span>';

        if($link || isset($options['icon_link'])) {
            echo '<div style="display: flex; background-color: #269abc; justify-content: center;">';
            if ($link) echo '<a href="' . $link . '" style="color: Black;">';
            echo '<h4>' . $name . '</h4>';
            if ($link) echo '</a>';

            if (isset($options['icon_link'])) {
                $optIconLink = $options['icon_link'];
                echo '<a href="' . $optIconLink['link'] . '" target="_blanl" style="margin-left: 8px; margin-right: 8px;">';
                echo '<img src="' . $optIconLink['icon'] . '" style="height: 21px; wisth: auto;" />';
                echo '</a>';
            }
            echo '</div>';
        }

        if($description) {
            echo '<p>' . $description . '</p>';
        }
    echo '</div>';

    if(isset($options['wins']) || isset($options['loses'])){
        $wins = isset($options['wins']) ? $options['wins'] : 0;
        $loses = isset($options['loses']) ? $options['loses'] : 0;

        echo '<div style="margin: auto; font-size: 32px; font-weight: bold; display: inherit;">';

        $iconStyle = "height: 64px; width: auto; margin-top: auto; margin-bottom: auto;";
        $valueStyle = "margin-top: auto; margin-bottom: auto; margin-left: 8px;";

        echo '<img src="img/strap.png" style="' . $iconStyle . '" />';
        echo '<span style="' . $valueStyle . ' color: #DC0083;">' . $wins . '</span>';

        echo '<img src="img/ccollar.png" style="' . $iconStyle . ' margin-left: 24px;" />';
        echo '<span style="' . $valueStyle . ' color: gray;">' . $loses . '</span>';

        echo '</div>';
        heightMargin(12);
    }

    if(isset($options['score'])){
        echo '<div>';
        echo '<div style="margin: auto; font-size: 32px; font-weight: bold; color: #674188;">'
            . formatMegaNumber($options['score']['main']) . '</div>';

        foreach ($options['score'] as $scKey => $scValue){
            if($scKey == 'main' || $scValue <= 0) continue;
            echo '<span style="margin-left: 4px; margin-right: 4px;">' .
                $scKey . ': ' . formatMegaNumber($scValue) . '</span>';
        }
        echo '</div>';
    }

    if(isset($options['rate'])){
        echo '<div style="margin: auto; font-size: 48px; font-weight: bold; color: #674188;">'
            . $options['rate'] . '</div>';
        heightMargin(12);
    }

    if(isset($options['related']) && $options['related'] && count($options['related']) > 0){
        $related = $options['related'];

        if($related[0] instanceof PesfemMedal){
            echo '<div class="card__footer" style="display: block; align-self: center; text-align: center">';
            $bigPrinted = false;
            foreach ($related as $item){
                $height = ($item->medal->level == 1) ? 90 : 60;
                $margin = ($item->medal->level == 1) ? 6 : 2;
                if($item->medal->level == 1) $bigPrinted = true;

                if($bigPrinted && $item->medal->level > 1){
                    $bigPrinted = false;
                    echo '<br>';
                }

                echo '<div class="user" style="display: inline-block">';
                echo '<img src="' . $item->medal->img . '" title="' . $item->title .
                    '" style="height:' . $height . 'px; width:auto; margin: ' . $margin . 'px;">';
                echo '</div>';
            }
            echo '</div>';
        }
        else {
            echo '<div class="card__footer">';
            foreach ($related as $item) {
                echo '<div class="user" style="';
                if(!empty($item[1]['winner'])) echo "background-color: magenta;";
                else if(!empty($item[1]['loser'])) echo "background-color: yellow;";
                else if(!empty($item[1]['featured'])) echo "background-color: crismon;";
                echo '">';

                if(!empty($item[1]['link'])){
                    echo '<a href="' . $item[1]['link'] . '" target="_blank" >';
                }
                echo '<img src="' . $item[0] . '" title="' . $item[1]['name'] . '" class="user__image">';
                echo '</div>';

                if(!empty($item[1]['link'])){
                    echo '</a>';
                }

            }
            echo '</div>';
        }
    }

    if(isset($options['more_medals'])){
        echo '<div style="margin: auto; font-size: 16px; font-weight: bold; display: flex;">';

        $iconStyle = "height: 64px; width: auto; margin-top: auto; margin-bottom: auto;";
        $valueStyle = "margin-top: auto; margin-bottom: auto; margin-left: 8px;";

        foreach ($options['more_medals'] as $medal){
            echo '<div style="margin-left: 2px; margin-right: 2px;">';
            echo '<img src="' . $medal['medal']->medal->img . '" style="' . $iconStyle
                . '" title="' . $medal['medal']->title . '" />';
            echo '<span style="' . $valueStyle . ' color: #DC0083;">' . $medal['number'] . '</span>';
            echo '</div>';
        }

        echo '</div>';
        heightMargin(12);
    }

    if(!empty($options['details'])){
        foreach ($options['details'] as $key => $value){
            echo '<div>';
            echo $key . ': ' . $value;
            echo '</div>';
        }
    }

    if(!empty($options['recent'])){
        $targetTime = $options['recent'];
        echo getRecentTime($targetTime);
    }

    /*<!--
    <div class="card__footer">
      <div class="user">
        <img src="avatars/1.jpg" alt="user__image" class="user__image">
        <div class="user__info">
          <h5>Jane Doe</h5>
          <small>2h ago</small>
        </div>
      </div>
    </div>
    -->*/

    echo '</div>';
}

function printBabeCard(PFBabe $babe, $index = null){
    printCard($babe->getAvatarSrc(),
        ($index !== null ? (($index + 1) . ': ') : '') . $babe->rawBabe['name'],
        null, "films.php?performer=" . $babe->pfID,
        [
            "related" => $babe->medals,
            "more_medals" => $babe->getMoreMedals(),
            "score" => [
                "main" => $babe->moviesPoints,
                "mis" => $babe->misPoints,
                "slv" => $babe->slvPoints,
                "warrior" => $babe->warriorPoints
            ],
            "magham" => $babe->getMaghamTitle(),
            "details" => [
                "Debut" => getRecentTime($babe->firstApp),
                "Last App" => getRecentTime($babe->lastApp),
                "Push" => $babe->getPushSpan(),
                "Duration" => $babe->getDurationSpan(),
            ]
        ]
    );
}

function printMovieCard(PFMovie $movie, $related, $index = null){
    printCard($movie->getAvatarSrc(),
        ($index !== null ? (($index + 1) . ': ') : '') . $movie->rawMovie['vidName'],
        $movie->rawMovie['comment'], $movie->getMoviePage(),
        [
            "tag" => $movie->pureScore,
            "related" => $related,
            "recent" => $movie->rawMovie['publish_time']
        ]
    );
}

function printQueendomCard($queendom, $related, $index = null){
    printCard(null,
        ($index !== null ? (($index + 1) . ': ') : '') . $queendom['name'],
        null, "qd.php?pk=" . $queendom['id'],
        [
            "icon_link" => ["icon" => "img/kiss.png", "link" => "queendom.php?pk=" . $queendom['id']]
        ]
    );
}
