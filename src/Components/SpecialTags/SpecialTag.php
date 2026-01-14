<?php
namespace Avetify\Components\SpecialTags;

use Avetify\Components\Buttons\JoshButton;
use Avetify\Components\JSField;
use Avetify\Interface\WebModifier;

class SpecialTag extends JSField {
    public function __construct(string $fieldId, public string $tagKey, public string $title,
                                public SpecialTags $parent, public bool $defValue = false){
        parent::__construct($fieldId);
    }

    public function basicJSRules(){
        ?>
        <script>
                <?php echo $this->parent->getJSObjectName(); ?>['<?php echo $this->fieldId ?>'] =
            <?php if($this->defValue) echo 'true'; else echo 'false'; ?>;
        </script>
        <?php
    }

    public function onClickRule(){
        ?>
        <script>
            document.getElementById("<?php echo $this->fieldId ?>").addEventListener("click", function(event) {
                    <?php echo $this->parent->getJSObjectName(); ?>['<?php echo $this->fieldId ?>'] =
                    !<?php echo $this->parent->getJSObjectName(); ?>['<?php echo $this->fieldId ?>'];
                const newValue = <?php echo $this->parent->getJSObjectName(); ?>['<?php echo $this->fieldId ?>'];

                if(newValue){
                    <?php if($this->parent->singleMode) {
                    foreach ($this->parent->tags as $tag) {
                    $childFieldId = $this->parent->getChildID($tag['key']);
                    $isEqual = $this->fieldId == $childFieldId;
                    if(!$isEqual) {
                    ?>

                        <?php echo $this->parent->getJSObjectName(); ?>['<?php echo $childFieldId ?>'] = false

                    <?php } } } ?>
                }

                <?php echo $this->parent->getJSRefreshName(); ?>();
            });
        </script>
        <?php
    }

    function place(?WebModifier $webModifier = null): void {
        $this->basicJSRules();
        if($webModifier == null) $webModifier = WebModifier::createInstance();
        if(!$this->parent->horizTags){
            $webModifier->styler->pushStyle("display", "block");
        }

        $joshButton = new JoshButton($this->title, $this->fieldId, "warning");
        $joshButton->place($webModifier);
        $this->moreJSRules();
        $this->onClickRule();
    }
}
