<?php

class SpecialTags extends JSField {
    public function __construct(string $fieldId, public array $tags){
        parent::__construct($fieldId);
    }

    public function basicJSRules(){
        ?>
        <script>
            <?php echo $this->getJSObjectName(); ?> = {}

            function <?php echo $this->getJSRefreshName(); ?>(){
                let tagElement = null
                let disp = ""
                <?php
                foreach ($this->tags as $tag){
                    $tagId = $this->getChildID($tag);
                ?>

                tagElement = document.getElementById('<?php echo $tagId; ?>');
                if(<?php echo $this->getJSObjectName(); ?>['<?php echo $tagId ?>']){
                    tagElement.classList.remove("warning")
                    if(disp) disp += ","
                    disp += '<?php echo $tag; ?>';
                }
                else {
                    tagElement.classList.add("warning")
                }

                <?php } ?>

                const dispElement = document.getElementById('<?php echo $this->getDisplayName(); ?>');
                const valElement = document.getElementById('<?php echo $this->fieldId ?>');

                dispElement.innerText = disp
                valElement.value = disp
            }
        </script>
        <?php
    }

    public function present(){
        $niceDiv = new NiceDiv(4);
        foreach ($this->tags as $tag){
            $specialTag = new SpecialTag($this->getChildID($tag), $tag, $this);
            $niceDiv->placeItem($specialTag);
        }
        $niceDiv->close();

        echo '<div ';
        HTMLInterface::addAttribute("id", $this->getDisplayName());
        Styler::startAttribute();
        Styler::addStyle("margin-top", "4px");
        Styler::closeAttribute();
        HTMLInterface::closeTag();
        HTMLInterface::closeDiv();

        FormUtils::placeHiddenField($this->fieldId, "", false);
    }

    public function getJSObjectName() : string {
        return "details__" . $this->fieldId;
    }

    public function getJSRefreshName() : string {
        return "refresh__" . $this->fieldId;
    }

    public function getDisplayName() : string {
        return "display__" . $this->fieldId;
    }
}

class SpecialTag extends JSField {
    public function __construct(string $fieldId, public string $title, public SpecialTags $parent){
        parent::__construct($fieldId);
    }

    public function basicJSRules(){
        ?>
        <script>
            <?php echo $this->parent->getJSObjectName(); ?>['<?php echo $this->fieldId ?>'] = false
        </script>
        <?php
    }

    public function onClickRule(){
        ?>
        <script>
            document.getElementById("<?php echo $this->fieldId ?>").addEventListener("click", function(event) {
                const fieldElement = document.getElementById("<?php echo $this->fieldId; ?>")
                <?php echo $this->parent->getJSObjectName(); ?>['<?php echo $this->fieldId ?>'] =
                    !<?php echo $this->parent->getJSObjectName(); ?>['<?php echo $this->fieldId ?>'];
                <?php echo $this->parent->getJSRefreshName(); ?>();
            });
        </script>
        <?php
    }

    function present(){
        $joshButton = new JoshButton($this->title, $this->fieldId, "warning");
        $joshButton->renderButton();
    }
}
