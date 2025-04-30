<?php

function printCard($img, $name, $description, $link, $options){
    $smallerTitle = !empty($options['smaller_title']);
    echo '<div class="card" ';
    if(!empty($options['context_menu'])){
        $omc = $options['context_menu']->openMenuJSCall($options['cmr_id']);
        HTMLInterface::addAttribute("oncontextmenu", $omc);
    }

    if(!empty($options['image_mode'])){
        Styler::startAttribute();
        Styler::addStyle("height", $options['image_height']);
        Styler::addStyle("width", $options['image_height'] * $options['width_multiplier']);
        Styler::closeAttribute();
    }
    HTMLInterface::closeTag();

    if($img) {
        echo '<div class="card__header">
        <img src="' . $img . '" alt="card__image" class="card__image" width="600" ';
        Styler::startAttribute();
        if(!empty($options['image_mode'])) {
            Styler::imageWithHeight($options['image_height']);
        }
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo '</div>';
    }

    echo '<div class="card__body">';
        if(isset($options['magham'])){
            echo '<div style="margin-top: -6px; font-weight: bold; font-size: 14px; color: firebrick">';
            echo $options['magham'];
            echo '</div>';
        }
        if(isset($options['tag'])) echo '<span class="tag tag-blue">' . $options['tag'] . '</span>';

        $descPrinted = false;
        $iconLinkExists = isset($options['icon_link']) || isset($options['more_icon_links']);
        if($link || $iconLinkExists) {
            $niceDiv = new NiceDiv(4);
            $niceDiv->addStyle("background-color", "#269abc");
            $niceDiv->addModifier("class", "contextmenu-exception");
            $niceDiv->open();

            if ($link){
                echo '<a ';
                HTMLInterface::addAttribute("href", $link);
                Styler::startAttribute();
                Styler::addStyle("color", "Black");
                Styler::addStyle("width", "82%");
                Styler::closeAttribute();
                HTMLInterface::closeTag();
            }
            if($name){
                printCardPureTitle($name, $smallerTitle);
            }
            else if($description){
                printCardPureTitle($description, $smallerTitle);
                $descPrinted = true;
            }
            if ($link) echo '</a>';

            if($iconLinkExists) {
                $allIconLinks = [$options['icon_link']];
                if (isset($options['more_icon_links']) && is_array($options['more_icon_links'])) {
                    $allIconLinks = array_merge($allIconLinks, $options['more_icon_links']);
                }

                foreach ($allIconLinks as $iconLink) {
                    echo '<a href="' . $iconLink['link'] . '" target="_blank" style="margin-left: 8px;">';
                    echo '<img src="' . $iconLink['icon'] . '" style="height: 21px; width: auto;" />';
                    echo '</a>';
                }
            }
            $niceDiv->close();
        }
        else printCardPureTitle($name, $smallerTitle);

        if($description && !$descPrinted) {
            printCardPureTitle($description, $smallerTitle);
        }

        if(!empty($options['placeables'])){
            foreach ($options['placeables'] as $placeable){
                $placeable->place();
            }
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
        HTMLInterface::placeVerticalDivider(12);
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
        HTMLInterface::placeVerticalDivider(12);
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

    if(isset($options['medals'])){
        echo '<div style="margin: auto; font-size: 16px; font-weight: bold; display: flex;">';

        $iconStyle = "height: 48px; width: auto; margin-top: auto; margin-bottom: auto;";
        $valueStyle = "margin-top: auto; margin-bottom: auto; margin-left: 8px; font-size: 1rem;";

        foreach ($options['medals'] as $medal){
            echo '<div style="margin-left: 6px; margin-right: 6px;">';
            if($medal->link){
                echo '<a href="' . $medal->link . '" target="_blank">';
            }
            echo '<img src="' . $medal->icon . '" style="' . $iconStyle
                . '" title="' . $medal->title . '" />';
            if($medal->link){
                echo '</a>';
            }
            echo '<span style="' . $valueStyle . ' color: #DC0083;">' . $medal->count . '</span>';
            echo '</div>';
        }

        echo '</div>';
        HTMLInterface::placeVerticalDivider(12);
    }

    if(isset($options['api_medals'])){
        $apiMedalsDiv = new NiceDiv(8);
        $apiMedalsDiv->open();

        foreach ($options['api_medals'] as $medalIndex => $apiMedal){
            if($medalIndex > 0) $apiMedalsDiv->separate();
            $apiMedal->present();
        }

        $apiMedalsDiv->close();
        HTMLInterface::placeVerticalDivider(12);
    }

    if(isset($options['span_texts'])){
        $tagsDiv = new NiceDiv(4);
        $tagsDiv->addModifier("id",
            (isset($options['stpk']) ? $options['stpk'] : slugify($name)) . "_" . "spans");
        $tagsDiv->open();

        foreach ($options['span_texts'] as $sptIndex => $spanText){
            if($sptIndex > 0) $tagsDiv->separate();
            $spanText->present();
        }

        $tagsDiv->close();
        HTMLInterface::placeVerticalDivider(12);
    }

    if(isset($options['api_texts'])){
        $apiTextsDiv = new VertDiv(6);
        $apiTextsDiv->open();

        foreach ($options['api_texts'] as $fieldIndex => $apiText){
            if($fieldIndex > 0) $apiTextsDiv->separate();
            $apiText->present();
        }

        $apiTextsDiv->close();
        HTMLInterface::placeVerticalDivider(12);
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

function printCardPureTitle($title, $smallerMode = false){
    echo '<div ';
    Styler::startAttribute();
    Styler::closeAttribute();
    HTMLInterface::closeTag();
    if(!$smallerMode) echo '<h4>' . $title . '</h4>';
    else {
        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle("font-weight", "bold");
        Styler::addStyle("font-size", "0.92rem");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        echo $title;
        HTMLInterface::closeDiv();
    }
    echo '</div>';
}
