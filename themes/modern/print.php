<?php

function printLabel($label, $src, $backgroundColor='black', $color='Cyan'){
    echo '<a href="' . $src . '" style="text-decoration: none;">';
    echo '<span class="label" style="background-color: ' . $backgroundColor . '; color: ' . $color . '; display: inline-block; margin: 1.5px;">';
    echo $label;
    echo '</span>';
    echo '</a>';
}

function printLinkedName($link, $title){
    echo '<a href="' . $link . '" target="_blank"';
    echo ' style="text-decoration: none;" >';
    echo '<span style="color: Black;">';
    echo $title;
    echo '</sapn>';
    echo '</a>';
}

function printTable($keys, $array, $options=Array()){
    $currentTime = time();

    if(!is_array($array) || count($array)<1){
        echo '<span style="font-weight:bold">';
        echo 'هنوز رکوردی ثبت نشده است';
        echo '</span>';
        return;
    }

    if(empty($options['sbsnum'])) $sbsNum = 1;
    else $sbsNum = $options['sbsnum'];

    echo '<table class="table"';
    echo ' style="';
    if(!empty($options['persian'])) echo 'font-family: IranSans; direction: rtl;';
    if(!empty($options['td_center'])) echo 'text-align: center;';
    echo '">';

    echo '<tr>';
    for($tn=0; $sbsNum>$tn; $tn++){
        if(!empty($options['have_row_number'])) echo '<th>Row</th>';
        if(!empty($options['have_row_number_persian'])) echo '<th>ردیف</th>';
        foreach($keys as $item){
            $item = (array) $item;
            echo '<th';
            if (isset($item['max-width'])) echo ' style="max-width: ' . $item['max-width'] . 'px; word-wrap: break-word;"';
            if (isset($item['width'])) echo ' style="width: ' . $item['width'] . 'px; word-wrap: break-word;"';
            echo '>'.$item['title'].'</th>';
        }
    }
    echo '</tr>';

    $whileIndex = 0;
    while(count($array) > $whileIndex){
        echo "<tr";
        if($sbsNum > 0 && !empty($options['is_followed_colored'])){
            if(!$array[$whileIndex]['is_followed']) echo ' style="background-color: GoldenRod;"';
        }
        echo '>';

        for($tn=0; $sbsNum>$tn; $tn++){
            $recordIndex = $whileIndex + $tn;
            if(!empty($options['have_row_number']) || !empty($options['have_row_number_persian']))
                echo '<td style="text-align: center;">'.($recordIndex+1).'</td>';

            if(count($array)>$recordIndex){
                $record = $array[$recordIndex];

                foreach($keys as $item){
                    $item = (array) $item;
                    $openTd = "<td";
                    if(!empty($item['fast_copy'])){
                        $openTd .= ' onclick="fastCopy(event)"';
                    }
                    $fullTd = $openTd . '>';

                    if(!empty($item['boolean'])){
                        echo $fullTd;
                        echo '<input type="checkbox" ';
                        if($record[$item['key']]) echo 'checked ';
                        echo 'onclick="return false;">';
                        echo '</td>';
                    }
                    else if(!empty($item['price'])){
                        $p = $record[$item['key']] . $record['currency'];

                        echo $fullTd;
                        echo $p;
                        echo '</td>';
                    }
                    else if(!empty($item['gheimat'])){
                        $p = $record[$item['key']];
                        $fp = number_format($p, 0, '', ',');

                        echo $fullTd;
                        echo $fp;
                        echo '</td>';
                    }
                    else if(!empty($item['timestamp'])){
                        $summaryTime = !empty($item['summary_time']);

                        echo $openTd;
                        if($record[$item['key']]){
                            $timeStr = jdate("y/m/d - H:i:s", $record[$item['key']], '', 'Asia/Tehran', 'en');
                            echo ' title="'.$timeStr.'"';
                        }
                        echo '>';

                        if($record[$item['key']]) {
                            $recentTime = getRecentTime($record[$item['key']]);
                            echo getRecentTimeString($recentTime, $summaryTime);
                        }
                        else echo 'Unknown';
                        echo '</td>';
                    }
                    else if(!empty($item['finish_timestamp'])){
                        $summaryTime = !empty($item['summary_time']);
                        $timeStr = jdate("y/m/d - H:i:s", $record[$item['key']], '', 'Asia/Tehran', 'en');
                        echo $openTd;
                        if(!$record[$item['key']]) echo ' style="background-color: Tomato;"';
                        else echo ' title="'.$timeStr.'"';
                        echo '>';

                        if(!$record[$item['key']]) echo "Nope";
                        else {
                            $recentTime = getRecentTime($record[$item['key']]);
                            echo getRecentTimeString($recentTime, $summaryTime);
                            echo '</td>';
                        }
                    }
                    else if(!empty($item['normal_time'])){
                        $summaryTime = !empty($item['summary_time']);

                        echo $openTd;
                        if($record[$item['key']]) {
                            $recentTime = getRecentTime($record[$item['key']]);
                            $recentTimeStr = getRecentTimeString($recentTime, $summaryTime);
                            echo ' title="' . $recentTimeStr . '"';
                        }
                        echo '>';

                        if(!($record[$item['key']] === null)) {
                            $format = $item['summary'] ? "Y/m/d" : "Y/m/d - H:i:s";

                            if ($record[$item['key']]) {
                                echo jdate($format, $record[$item['key']], '', 'Asia/Tehran', 'en');
                            } else echo "Unknown";
                        }
                        echo '</td>';
                    }
                    else if(!empty($item['finish_normal_time'])){
                        $summaryTime = !empty($item['summary_time']);
                        $recentTime = getRecentTime($record[$item['key']]);
                        $recentTimeStr = getRecentTimeString($recentTime, $summaryTime);
                        echo $openTd;
                        if(!$record[$item['key']]) echo ' style="background-color: Tomato;"';
                        else echo ' title="'.$recentTimeStr.'"';
                        echo '>';
                        if($record[$item['key']])
                            echo jdate("y/m/d - H:i:s", $record[$item['key']], '', 'Asia/Tehran', 'en');
                        else echo "ناتمام";
                        echo '</td>';
                    }
                    else if(!empty($item['duration'])){
                        echo $openTd;
                        if($record[$item['key']] < 0) echo ' style="background-color: Tomato;"';
                        echo '>';
                        if($record[$item['key']] >= 0) echo $record[$item['key']] . 's';
                        else echo "ناتمام";
                        echo '</td>';
                    }
                    else if(!empty($item['progress'])){
                        echo $openTd;
                        echo '>';
                        echo '<progress value="' . $record[$item['key']] . '" max="100">' . $record[$item['key']] . '% </progress>';
                        echo '</td>';
                    }
                    else if(!empty($item['date'])){
                        echo $openTd . ' style="text-align: center;">';
                        echo date('Y-M-d', $record[$item['key']]);
                        endline(); echo date('h:i', $record[$item['key']]);
                        echo '</td>';
                    }
                    else if(!empty($item['rating'])){
                        $ratingType = $item['rating_type'];
                        $ratingField = $item['rating_field'];
                        $ratingValue = isset($record["ratings"][$ratingType][$ratingField]) ?
                            $record["ratings"][$ratingType][$ratingField] : 0;

                        $bg = "Cyan";
                        if($ratingField != "games_count") {
                            if ($ratingValue <= 850) $bg = '#EF5A6F';
                            else if ($ratingValue <= 1100) $bg = 'GoldenRod';
                            else if ($ratingValue <= 1250) $bg = 'PaleGreen';
                            else if ($ratingValue <= 1400) $bg = 'LimeGreen';
                        }
                        else {
                            if ($ratingValue <= 200) $bg = '#EF5A6F';
                            else if ($ratingValue <= 1000) $bg = 'GoldenRod';
                            else if ($ratingValue <= 2500) $bg = 'PaleGreen';
                            else if ($ratingValue <= 6000) $bg = 'LimeGreen';
                        }

                        echo $openTd . ' style="text-align: center; background-color: ' . $bg . '">';
                        echo (int) $ratingValue;
                        echo '</td>';
                    }
                    else if(!empty($item['cdc'])){
                        $username = $record[$item['key']];
                        $ps = new PlayerStats($username);
                        echo $openTd . ' style="text-align: center; max-width: 150px;">';
                        $ps->printLinkedCDCUsername();
                        echo '</td>';
                    }
                    else if(!empty($item['warrior'])){
                        $name = $record[$item['key']];
                        $link = "films.php?performer=" . $record['pfID'];
                        echo $openTd . ' style="text-align: center; max-width: 150px;">';
                        printLinkedName($link, $name);
                        echo '</td>';
                    }
                    else if(!empty($item['warrior_avatar'])){
                        echo $openTd . ' style="text-align: center; max-width: 150px;">';
                        echo '<img src="avatars/' . $record['pfID'] . '.jpg' . '" ';
                        echo 'style="height: 100px; width: auto;" />';
                        echo '</td>';
                    }
                    else if(!empty($item['days'])){
                        echo $openTd . ' style="text-align: center;">';
                        if(!$record[$item['key']]) echo "Today";
                        else if($record[$item['key']] == 1) echo "Yesterday";
                        else echo $record[$item['key']] . " Days Ago";
                        echo '</td>';
                    }
                    else if(!empty($item['days_persian'])){
                        echo $openTd . ' style="text-align: center; direction: rtl;">';
                        if(!$record[$item['key']]) echo "امروز";
                        else if($record[$item['key']] == 1) echo "دیروز";
                        else echo $record[$item['key']] . " روز قبل";
                        echo '</td>';
                    }
                    else if(!empty($item['hours'])){
                        echo $openTd . ' style="text-align: center;">';
                        if(!$record[$item['key']]) echo "This Hour";
                        else if($record[$item['key']] == 1) echo "1 Hour Ago";
                        else echo $record[$item['key']] . " Hours Ago";
                        echo '</td>';
                    }
                    else if(!empty($item['hours_persian'])){
                        echo $openTd . ' style="text-align: center; direction: rtl;">';
                        if(!$record[$item['key']]) echo "همین ساعت";
                        else if($record[$item['key']] == 1) echo "1 ساعت قبل";
                        else echo $record[$item['key']] . " ساعت قبل";
                        echo '</td>';
                    }
                    else if(!empty($item['decimals'])){
                        echo $fullTd;
                        echo doubleToString($record[$item['key']], 2);
                        echo '</td>';
                    }
                    else if(!empty($item['percent'])){
                        $numericValue = isset($record[$item['key']]) && is_numeric($record[$item['key']]) ?
                            $record[$item['key']] : 0;

                        $value = isset($record[$item['key']]) && is_numeric($record[$item['key']]) ?
                            $record[$item['key']] . '%' : "-";

                        echo $openTd;
                        if($numericValue <= 20) echo ' style="background-color: Tomato;"';
                        else if($numericValue <= 40) echo ' style="background-color: GoldenRod;"';
                        else if($numericValue <= 60) echo ' style="background-color: PaleGreen;"';
                        else if($numericValue <= 80) echo ' style="background-color: LimeGreen"';
                        else echo ' style="background-color: Cyan;"';
                        echo '>';
                        echo '<div style="width: 100%; text-align: center">';
                        echo $value;
                        echo '</div>';
                        echo '</td>';
                    }
                    else if(!empty($item['overall'])){
                        $numericValue = $record[$item['key']];

                        echo $openTd;
                        if($numericValue <= 69) echo ' style="background-color: Tomato;"';
                        else if($numericValue <= 74) echo ' style="background-color: GoldenRod;"';
                        else if($numericValue <= 79) echo ' style="background-color: #FFEEAD"';
                        else if($numericValue <= 84) echo ' style="background-color: PaleGreen;"';
                        else if($numericValue <= 89) echo ' style="background-color: LimeGreen"';
                        else if($numericValue <= 94) echo ' style="background-color: Cyan"';
                        else echo ' style="background-color: #7A1CAC; color: White;"';
                        echo '>';
                        echo '<div style="width: 100%; text-align: center">';
                        echo $numericValue;
                        echo '</div>';
                        echo '</td>';
                    }
                    else if(!empty($item['message'])){
                        echo $openTd;
                        echo ' style="direction: ltr; text-align: left;';
                        if(isset($item['max-width'])) echo ' max-width: ' . $item['max-width'] . 'px; word-wrap: break-word;';
                        echo '"';
                        echo ' title="'.$record[$item['key']].'"';
                        echo '>';

                        $message = $record[$item['key']];

                        if(!empty($record['session']) && $record['session'] == UNFOLLOW_OPERATION){
                            $last = strrpos($message, '-');
                            if($last >= 2){
                                $message = substr($message, 0,$last - 1);
                            }
                        }

                        if(!(stripos($message, "No response from server") === false)){
                            $message = "No response from server.";
                        }
                        else if(!(stripos($message, "Requested resource does not exist") === false)){
                            $message = "Requested resource does not exist.";
                        }
                        else if(!(stripos($message, "CURL error") === false)){
                            $message = "Curl Error.";
                        }
                        else if(stripos($message, "InstagramAPI") === 0 && strpos($message, ":") != 0){
                            $start = strpos($message, ":");
                            if($start >= (strlen($message) - 2)) $start = 0;
                            $message = substr($message, $start + 1);
                        }

                        $message = trim($message);

                        if(strlen($message)<=100) echo wordwrap($message, 60, "<br>");
                        else echo substr($message, 0, 98) . ' ...';
                        echo '</td>';
                    }
                    else if(!empty($item['caption'])){
                        echo $openTd;
                        echo ' style="direction: rtl;';
                        if(isset($item['max-width'])) echo ' max-width: ' . $item['max-width'] . 'px; word-wrap: break-word;';
                        echo '">';
                        echo wordwrap($record[$item['key']], 40, "<br>", true);
                        echo '</td>';
                    }
                    else if(!empty($item['error'])){
                        echo $openTd;
                        if($record[$item['key']]) echo ' style="background-color: Tomato;"';
                        if(!empty($item['logs_key']) && !empty($record[$item['logs_key']]))
                            echo ' title="' . $record[$item['logs_key']] . '"';
                        echo '>';
                        echo $record[$item['key']];
                        echo '</td>';
                    }
                    else if(!empty($item['warning'])){
                        echo $openTd;
                        if($record[$item['key']]) echo ' style="background-color: GoldenRod;"';
                        echo '>';
                        echo $record[$item['key']];
                        echo '</td>';
                    }
                    else if(!empty($item['image'])){
                        echo $openTd . ' style="text-align: center;">';
                        echo '<a href="';
                        if(!empty($record['video'])) echo $record['video'];
                        else echo $record[$item['key']];
                        echo '" target="_blank"><img src="'.$record[$item['key']].
                            '" style="width: 240px; height: auto;"></a>';
                        echo '</td>';
                    }
                    else if(!empty($item['footballer_name'])){
                        echo $openTd . ' style="text-align: center;">';
                        printFootballerLink($record, false);
                        echo '</td>';
                    }
                    else if(!empty($item['unlocking'])){
                        echo $openTd . ' style="text-align: center;">';
                        if($record['unlocking']){
                            $ulSet = explode(";", $record['unlocking']);
                            $pr = 0;
                            foreach ($ulSet as $ul){
                                if($pr > 0) echo '<br>';
                                $bgColor = ($pr % 2) == 0 ? "#FFF" : "#E8EBEF";
                                $pr++;

                                echo '<span style="font-size: 14px; background-color: ' .
                                    $bgColor . ';">';
                                echo $pr . ': ' . $ul;
                                echo '</span>';
                            }
                        }
                        echo '</td>';
                    }
                    else if(!empty($item['tekken_entity_name'])){
                        echo $openTd . ' style="text-align: center;">';

                        if(isset($item['entity']) && $item['entity'] == "puzzle"){
                            printTekkenPuzzleLink($record, false);
                        }
                        else printTekkenCharacterLink($record, false);
                        echo '</td>';
                    }
                    else if(!empty($item['movie_pk'])){
                        echo $openTd . ' style="text-align: center;">';
                        printMovieLink($record, false, $record['pk']);
                        echo '</td>';
                    }
                    else if(!empty($item['tekken_normal_field'])){
                        echo $openTd . ' style="text-align: center;">';
                        $fieldName = $item['pre_name'] . $record['pk'];

                        echo '<input type="text" name="' . $fieldName . '" value="' .
                            $record[$item['value']] . '"' .
                            (($item['rtl']) ? ' dir="rtl"' : "") .
                            ' placeholder="' . $item['placeholder'] . '" style="height: 35px;" />';

                        echo '</td>';
                    }
                    else if(!empty($item['tekken_per_bio_field'])){
                        echo $openTd . ' style="text-align: center;">';
                        $fieldName = 'per_bio_' . $record['pk'];

                        echo '<textarea name="' . $fieldName . '" dir="rtl" placeholder="بایو" cols="60" rows="8" >';
                        echo $record['per_bio'];
                        echo '</textarea>';

                        echo '</td>';
                    }
                    else if(!empty($item['form_field'])){
                        echo $openTd . ' style="text-align: center;">';
                        $fieldName = $item['pre_id'] . $record[$item['primary_key']];

                        echo '<input type="text" name="' . $fieldName . '" value="'
                            . $record[$item['key']] . '" dir="ltr" placeholder="'
                            . $item['placeholder'] . '" style="height: 35px;';
                        echo 'width: ' . $item['width'] . 'px;';
                        echo '" />';

                        echo '</td>';
                    }
                    else if(!empty($item['checkbox_field'])){
                        echo $openTd . ' style="text-align: center;">';
                        $fieldName = $item['pre_id'] . $record[$item['primary_key']];

                        echo '<input type="checkbox" name="' . $fieldName . '" value="1"';
                        if($record[$item['key']]) echo ' checked';
                        echo ' style="height: 30px; width: 30px;';
                        echo '" />';

                        echo '</td>';
                    }
                    else if(!empty($item['footballer_per_name_field'])){
                        echo $openTd . ' style="text-align: center;">';
                        $fieldName = 'per_name_' . $record['pk'];

                        echo '<input type="text" name="' . $fieldName . '" value="' .
                            $record['per_name'] . '" dir="rtl" placeholder="نام فارسی" style="height: 35px;" />';

                        echo '</td>';
                    }
                    else if(!empty($item['club_per_name_field'])){
                        echo $openTd . ' style="text-align: center;">';
                        $fieldName = 'per_name_' . $record['ftid'];

                        echo '<input type="text" name="' . $fieldName . '" value="' .
                            $record['per_alt'] . '" dir="rtl" placeholder="نام فارسی" style="height: 35px;" />';

                        echo '</td>';
                    }
                    else if(!empty($item['country_per_name_field'])){
                        echo $openTd . ' style="text-align: center;">';
                        $fieldName = 'per_name_' . $record['slug'];

                        echo '<input type="text" name="' . $fieldName . '" value="' .
                            $record['per_name'] . '" dir="rtl" placeholder="نام فارسی" style="height: 35px;" />';

                        echo '</td>';
                    }
                    else if(!empty($item['club_country_field'])){
                        echo $openTd . ' style="text-align: center;">';
                        $fieldName = 'club_country_' . $record['ftid'];
                        $clubCountryFlag = $record['country'] ? 'avatars/nations/' . $record['country'] . '.png'
                            : "img/x.png";
                        $bg = $record['country'] ? 'white' : "yellow";

                        ?>
                            <img src="<?php echo $clubCountryFlag; ?>" style="height: 30px; width: auto; margin-bottom: 8px;" />

                            <input type="text" id="<?php echo $fieldName; ?>" style="height: 35px; background-color: <?php echo $bg; ?>;"
                                   name="<?php echo $fieldName; ?>" list="nations"
                                   autocomplete="off" placeholder="Nation" class="empty" oninput=""
                                   value="<?php echo $record['country']; ?>" />&nbsp;&nbsp;
                        <?php

                        echo '</td>';
                    }
                    else if(!empty($item['country_continent_field'])){
                        echo $openTd . ' style="text-align: center;">';
                        $fieldName = 'country_continent_' . $record['slug'];
                        /*$clubCountryFlag = $record['country'] ? 'avatars/nations/' . $record['country'] . '.png'
                            : "img/x.png";*/
                        $bg = $record['continent'] ? 'white' : "yellow";

                        ?>
                        <input type="text" id="<?php echo $fieldName; ?>" style="height: 35px; background-color: <?php echo $bg; ?>;"
                               name="<?php echo $fieldName; ?>" list="continents"
                               autocomplete="off" placeholder="Continent" class="empty" oninput=""
                               value="<?php echo $record['continent']; ?>" />&nbsp;&nbsp;
                        <?php

                        echo '</td>';
                    }
                    else if(!empty($item['footballer_avatar'])){
                        $avatar = "avatars/footballers/" . $record['ftid'] . '.png';
                        echo $openTd . ' style="text-align: center;">';
                        //echo '<a href="';
                        //echo $avatar . '" target="_blank">';
                        echo '<img src="'.$avatar.
                            '" style="width: 120px; height: auto;">';
                        //echo '</a>';
                        echo '</td>';
                    }
                    else if(!empty($item['tekken_entity_avatar'])){
                        $avatar = "avatars/" . $item['entity'] . "s/" . $record['pk'] . '.png';
                        echo $openTd . ' style="text-align: center;">';
                        //echo '<a href="';
                        //echo $avatar . '" target="_blank">';
                        echo '<img src="'.$avatar.
                            '" style="width: 120px; height: auto;">';
                        //echo '</a>';
                        echo '</td>';
                    }
                    else if(!empty($item['movie_avatar'])){
                        $avatar = "images/altwebp/" . $record['imdb_id'] . '.webp';

                        if(!file_exists($avatar)) {
                            $avatar = "images/webp/" . $record['imdb_id'] . '.webp';
                        }

                        echo $openTd . ' style="text-align: center;">';
                        echo '<img src="'.$avatar.
                            '" style="width: 120px; height: auto;">';
                        echo '</td>';
                    }
                    else if(!empty($item['club_avatar'])){
                        $avatar = "avatars/clubs/" . $record['ftid'] . '.png';
                        $link = "club.php?ftid=" . $record['ftid'];
                        echo $openTd . ' style="text-align: center;">';
                        echo '<a href="';
                        echo $link;
                        echo '" target="_blank"><img src="'.$avatar.
                            '" style="width: 120px; height: auto;"></a>';
                        echo '</td>';
                    }
                    else if(!empty($item['country_flag'])){
                        $countryCode = $record[$item['key']];
                        $avatar = "img/flags/" . $countryCode . '.png';
                        echo $openTd . ' style="text-align: center;">';
                        echo '<img src="'.$avatar. '" style="height: 25px; width: auto;">';
                        echo '</td>';
                    }
                    else if(!empty($item['ft_slug'])){
                        echo $openTd . ' style="text-align: center;">';
                        echo '<a href="' . FTF_URL . $record['slug'] . '" style="text-decoration: none; color:black;" target="_blank">';
                        echo $record['slug'];
                        echo '</a>';
                        //prLink(FTF_URL . $record['slug'], $record['slug'], false, false, true, true);
                        echo '</td>';
                    }
                    else if(!empty($item['footballer_clubs'])){
                        echo $openTd . ' style="text-align: center;">';
                        printFootballerClubs($record);
                        echo '</td>';
                    }
                    else if(!empty($item['player_icon'])){
                        echo $openTd . ' style="text-align: center;">';
                        if($record['featured_box_id']){
                            /*$cardBG = 'https://www.pesmaster.com/pes-2020/graphics/cards/' .
                                sprintf("%'03d", $record['featured_box_id']) . '.png';*/
                            $cardBG = 'assets/img/cards/featuredBG.png';
                        }
                        else if($record['type'] == 'Featured Players'){
                            $cardBG = 'assets/img/cards/featuredBG.png';
                        }
                        else if($record['type'] == 'Legend'){
                            $cardBG = 'assets/img/cards/legendsBG.png';
                        }
                        else $cardBG = 'assets/img/cards/baseBG.png';

                        if($record['card_head']){
                            $image = "https://www.pesmaster.com/" . $record['card_head'];
                            $image = str_replace('_l.png', '.png', $image);
                        }
                        else if($record['face'])
                            $image = 'https://www.pesmaster.com/pes-2020/graphics/players/player_' . $record['id'] . '.png';
                        else $image = 'assets/img/ukface.png';
                        echo '<img src="' .$image. '" style="width: 60px; height: auto;' .
                            ' background-image: url(' . $cardBG . '); background-size: cover;"' .
                            ' onclick="copyTextToClipboard(' . $record['id'] . ');">';
                        echo '</td>';
                    }
                    else if(!empty($item['post'])){
                        if($record[$item['key']] == 0) $postStr = "GK";
                        else if($record[$item['key']] == 1) $postStr = "CB";
                        else if($record[$item['key']] == 2) $postStr = "LB";
                        else if($record[$item['key']] == 3) $postStr = "RB";
                        else if($record[$item['key']] == 4) $postStr = "DMF";
                        else if($record[$item['key']] == 5) $postStr = "CMF";
                        else if($record[$item['key']] == 6) $postStr = "LMF";
                        else if($record[$item['key']] == 7) $postStr = "RMF";
                        else if($record[$item['key']] == 8) $postStr = "AMF";
                        else if($record[$item['key']] == 9) $postStr = "LWF";
                        else if($record[$item['key']] == 10) $postStr = "RWF";
                        else if($record[$item['key']] == 11) $postStr = "SS";
                        else if($record[$item['key']] == 12) $postStr = "CF";
                        else $postStr = "UK";

                        echo $openTd . ' style="text-align: center;">';
                        echo $postStr;
                        echo '</td>';
                    }
                    else if(!empty($item['club'])){
                        if($record['type'] == "Legend") $icon = "assets/img/legend.png";
                        else if($record['type'] == "Featured Players" || $record['is_featured'])
                            $icon = "assets/img/featured_player.png";
                        else $icon = "";

                        echo $openTd . ' style="text-align: center;">';
                        echo $record['club'];

                        if($icon) echo '<img src="' . $icon . '" style="width: 25px; height: auto;">';
                        echo '</td>';
                    }
                    else if(!empty($item['player_name'])){
                        echo $openTd . ' style="text-align: center;">';
                        echo '<a href="https://www.pesmaster.com' . $record['link'] . '" style="text-decoration: none; color:black;">';
                        echo $record['name'];
                        echo '</a><br>';
                        echo '<a href="http://pesdb.net/pes2020/?id=' . $record['id'] . '" target="_blank">';
                        echo '<img src="assets/img/link.png" style="height: 16px; width: auto; margin-top: 12px;">';
                        echo '</a>';
                        echo '</td>';
                    }
                    else if(!empty($item['target'])){
                        echo $openTd . ' style="text-align: center; background-color: GoldenRod; color: #FFFFFF; font-weight: bold;">';
                        echo $record[$item['key']];
                        echo '</td>';
                    }
                    else{
                        echo $openTd . ' style="text-align: center;';
                        if(isset($item['max-width'])) echo ' max-width: ' . $item['max-width'] . 'px; word-wrap: break-word;';
                        echo '">';
                        $value = isset($record[$item['key']]) ? $record[$item['key']] : "-";
                        echo $value;
                        echo '</td>';
                    }
                }
            }
            else{
                foreach($keys as $item) echo '<td></td>';
            }
        }
        echo "</tr>";

        $whileIndex = $recordIndex + 1;
    }
    echo '</table>';
}

function printFootballerLink($f, $bold = true){
    prLink("edit.php?footballer=" . $f['pk'], $f['name'], "Black", $bold, true, true);
}

function redirectToFootballer($footballerPk){
    $link = "edit.php?footballer=" . $footballerPk;
    safe_redirect($link);
    exit();
}

function printCountryFlag($countrySlug, $sizeMultiplier = 1, $countryPrePath = ""){
    if(!$countrySlug) return;

    $flagHeight = 30;
    if($sizeMultiplier != 1){
        $flagHeight *= $sizeMultiplier;
        $flagHeight = (int) $flagHeight;
    }

    $flag = $countryPrePath . "avatars/nations/" . $countrySlug . '.png';
    echo '<img src="'.$flag.
        '" style="height: ' . $flagHeight . 'px; width: auto;" title="' . $countrySlug . '">';
}

function printClubLink($ftid, $name, $height){
    $avatar = "avatars/clubs/" . $ftid . '.png';

    echo '<a href="' . 'club.php?ftid=' . $ftid . '" target="_blank">';
    echo '<img src="'.$avatar.
        '" style="height: ' . $height . 'px; width: auto; margin-left: 2px; margin-right: 2px;" title="' . $name . '">';
    echo '</a>';
}

function printCountryLink($slug, $name, $height){
    $avatar = "avatars/nations/" . $slug . '.png';

    echo '<a href="' . 'country.php?slug=' . $slug . '" target="_blank">';
    echo '<img src="'.$avatar.
        '" style="height: ' . $height . 'px; width: auto; margin-left: 2px; margin-right: 2px;" title="' . $name . '">';
    echo '</a>';
}

function printClubs($clubs, $sizeMultiplier, $clubLimit){
    $ids = "";
    $names = "";
    $disabled = "";

    foreach ($clubs as $club){
        if($ids){
            $ids .= ',';
            $names .= ',';
            $disabled .= ',';
        }

        $ids .= $club['club_ftid'];
        $names .= $club['club_name'];
        $disabled .= $club['disabled'];

        $sizeMultiplier = $sizeMultiplier != 1 ? $sizeMultiplier : 1.05;
        $f = ["club_ftid" => $ids, "club_names" => $names, "clubs_disabled" => $disabled];
    }

    printFootballerClubs($f, $sizeMultiplier, $clubLimit);
}

function printFootballerClubs($footballer, $sizeMultiplier = 1, $clubLimit = 4){
    $clubHeight = 60;
    $dividerHeight = 18;

    if($sizeMultiplier != 1){
        $clubHeight *= $sizeMultiplier;
        $dividerHeight *= $sizeMultiplier;

        $clubHeight = (int) $clubHeight;
        $dividerHeight = (int) $dividerHeight;
    }

    if($sizeMultiplier == 1) {
        printCountryFlag($footballer['nation_slug'], $sizeMultiplier);
        echo '<br>';
        echo '<div style="height: ' . $dividerHeight . 'px;"></div>';
    }

    $clubIds = explode(",", $footballer['club_ftid']);
    $clubNames = explode(",", $footballer['club_names']);
    $clubsDisabled = explode(",", $footballer['clubs_disabled']);

    $ig = 0;
    for($i=0; count($clubIds)>$i; $i++){
        $ftid = $clubIds[$i];

        if($ftid == 'team' || $clubsDisabled[$i]){
            $ig++;
            continue;
        }

        if($i > 0){
            $prevFtid = $clubIds[$i - 1];
            if(!$clubsDisabled[$i - 1] && $ftid == $prevFtid){
                $ig++;
                continue;
            }
        }

        $clubName = $clubNames[$i];
        printClubLink($ftid, $clubName, $clubHeight);
        if((($i + 1 - $ig) % $clubLimit) == 0) echo '<br>';
    }
}

function prLink($href, $str = false, $color = false, $bold = false, $blank = false, $noDecor = false){
    if($str===false) $str=$href;
    echo '<a href="'.$href.'"';
    if($blank) echo ' target="_blank"';
    if($noDecor) echo ' style="text-decoration: none;!important"';
    echo '>';
    if(!($color===false)) echo '<font color="'.$color.'">';
    if($bold) echo '<b>';
    echo $str;
    if($bold) echo '</b>';
    if(!($color===false)) echo '</font>';
    echo '</a>';
}

function heightMargin(int $height){
    echo '<div style="height: ' . $height . 'px;"></div>';
}

function echoTH($str, $col, $cspan = 0)
{
    $num = $col - strlen($str);
    if ($num < 0) $num = 0;
    echo "<th";
    if ($cspan != 0) echo ' colspan="' . $cspan . '"';
    echo '>';
    for ($i = 0; (int)($num / 2) > $i; $i++) echo "&nbsp;";
    if ($num % 2 == 1) echo "&nbsp;";
    echo $str;
    for ($i = 0; (int)($num / 2) > $i; $i++) echo "&nbsp;";
}

function printPreArray($array, $name = "Array")
{
    echo '<div style="margin-top: 20px; margin-bottom: 20px; text-align: left; background-color: #dee9e7">';
    echo '##' . $name . '<br><pre>';
    print_r($array);
    echo '</pre></div>';
}

function importantEcho($str, $color, $beforeBR = 1, $afterBR = 0)
{
    for ($i = 0; $beforeBR > $i; $i++) echo '<br>';
    echo '<b><font color="' . $color . '">' . $str . '</font></b>';
    for ($i = 0; $afterBR > $i; $i++) echo '<br>';
}

function typeDash($num, $befbr, $afbr, $bold = false)
{
    if ($bold) echo "<b>";
    for ($i = 0; $befbr > $i; $i++) echo '<br>';
    for ($i = 0; $num > $i; $i++) echo '-';
    for ($i = 0; $afbr > $i; $i++) echo '<br>';
    if ($bold) echo "</b>";
}

function printNum($num)
{
    if ($num > 9999999999) {
        $print = round($num / 1000000000, 0);
        echo $print, "B";
    } else if ($num > 999999999) {
        $print = round($num / 1000000000, 1);
        echo $print, "B";
    } else if ($num > 9999999) {
        $print = round($num / 1000000, 0);
        echo $print, "M";
    } else if ($num > 999999) {
        $print = round($num / 1000000, 1);
        echo $print, "M";
    } else if ($num > 9999) {
        $print = round($num / 1000, 0);
        echo $print, "K";
    } else if ($num > 999) {
        $print = round($num / 1000, 1);
        echo $print, "K";
    } else if ($num != null && $num != "") echo $num;
    else echo "UK";
}

function imageLink($imgSrc, $href, $style = "")
{
    echo '<a href="', $href, '">';
    echo '<img src="', $imgSrc, '" style="' . $style . '">';
    echo '</a>';
}

function printStat($name, $stat, $nameColor, $statColor, $br = true)
{
    echo '<b><font color="' . $nameColor . '">';
    echo $name . ': ';
    echo '</font></b>';

    echo '<b><font color="' . $statColor . '">';
    if ($stat === true) echo "Yes";
    else if ($stat === false) echo "No";
    else echo $stat;
    if ($br) endline();
    echo '</font></b>';
}

