<?php

class CroppableImage {
    public function __construct(public string $serverSrc, public string $id,
                                public int $imageType = IMAGETYPE_JPEG,
                                public float $targetRatio = 0,
                                public float $originalRatio = 0
    ){
        if(!$this->originalRatio){
            $this->originalRatio = ImageUtils::getRatio($this->serverSrc);
        }
    }

    private function _present(int $size, bool $withWidth = true){
        ?>
        <script>
            <?php echo $this->getJSRatioVarName(); ?> = <?php echo $this->targetRatio; ?>;
        </script>
        <?php

        echo '<div>';
        echo '<img ';

        $imageSrc = $this->serverSrc . '?' . time();
        HTMLInterface::addAttribute("src", Routing::serverPathToBrowserPath($imageSrc));
        HTMLInterface::addAttribute("id", $this->id);
        HTMLInterface::addAttribute("onclick", "setCropConfigs(event, '" . $this->id .
            "', " . $this->getJSRatioVarName() . ")");
        Styler::startAttribute();
        if($withWidth) Styler::imageWithWidth($size);
        else Styler::imageWithHeight($size);
        Styler::closeAttribute();
        echo ' />';

        echo '<div ';
        HTMLInterface::addAttribute("id", $this->id . '_status');
        Styler::startAttribute();
        Styler::addStyle("font-size", "0.5rem");
        Styler::closeAttribute();
        echo ' >';
        echo '</div>';
        echo '</div>';
        FormUtils::placeHiddenField($this->id . '_x', 0);
        FormUtils::placeHiddenField($this->id . '_y', 0);
        FormUtils::placeHiddenField($this->id . '_w', 0);
        FormUtils::placeHiddenField($this->id . '_h', 0);
        FormUtils::placeHiddenField($this->id . '_url', $this->serverSrc);
        FormUtils::placeHiddenField($this->id . '_enabled', 0);
    }

    public function presentFromWidth(int $size){
        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle("width", $size . "px");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        $this->_present($size, true);
        HTMLInterface::closeDiv();
    }

    public function presentFromHeight(int $size){
        $widthSize = $size * $this->originalRatio;
        echo '<div ';
        Styler::startAttribute();
        Styler::addStyle("width", $widthSize . "px");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        $this->_present($size, false);
        HTMLInterface::closeDiv();
    }

    public function checkSubmit() : bool {
        $enabledFieldKey = $this->id . '_enabled';
        if(!empty($_POST[$enabledFieldKey])){
            return $this->handleSubmit(
                (int) $_POST[$this->id . "_x"],
                (int) $_POST[$this->id . "_y"],
                (int) $_POST[$this->id . "_w"],
                (int) $_POST[$this->id . "_h"],
            );
        }
        return false;
    }

    public function handleSubmit($x, $y, $w, $h) : bool {}

    public function getJSRatioVarName() : string {
        return $this->id . "__ration";
    }
}
