<?php
namespace Avetify\Components\SpecialTags;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Components\Containers\VertDiv;
use Avetify\Components\JSField;
use Avetify\Forms\FormUtils;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;

class SpecialTags extends JSField {
    public int $lineItemsLimit = 8;

    public function __construct(string $fieldId, public array $tags,
                                public bool $horizTags,
                                public $curRecord,
                                public bool $singleMode = false,
                                public bool $displayTags = false){
        parent::__construct($fieldId);
        $this->lineItemsLimit = $this->horizTags ? 15 : 22;
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
                    $tagId = $this->getChildID($tag['key']);
                ?>

                tagElement = document.getElementById('<?php echo $tagId; ?>');
                if(<?php echo $this->getJSObjectName(); ?>['<?php echo $tagId ?>']){
                    tagElement.classList.remove("warning")
                    if(disp) disp += ",";
                    disp += '~';
                    disp += '<?php echo $tag['key']; ?>';
                    disp += '~';
                }
                else {
                    tagElement.classList.add("warning")
                }

                <?php } ?>

                const valElement = document.getElementById('<?php echo $this->fieldId ?>');
                valElement.value = disp

                <?php if($this->displayTags) { ?>
                    const dispElement = document.getElementById('<?php echo $this->getDisplayName(); ?>');
                    dispElement.innerText = disp
                <?php } ?>
            }
        </script>
        <?php
    }

    public function place(?WebModifier $webModifier = null){
        $this->basicJSRules();
        $tagsDiv = $this->horizTags ? new NiceDiv(4) : new VertDiv(4);
        $tagsDiv->open();
        foreach ($this->tags as $index => $tag){
            if($index != 0 && ($index % $this->lineItemsLimit) == 0){
                $tagsDiv->close();
                if($this->horizTags) HTMLInterface::placeVerticalDivider(6);
                else HTMLInterface::placeHorizontalDivider(6);
                $tagsDiv->open();
            }
            $specialTag = $this->createChildTag($tag);
            $tagsDiv->placeItem($specialTag);
        }
        $tagsDiv->close();

        if($this->displayTags) {
            echo '<div ';
            HTMLInterface::addAttribute("id", $this->getDisplayName());
            Styler::startAttribute();
            Styler::addStyle("margin-top", "4px");
            Styler::closeAttribute();
            HTMLInterface::closeTag();
            HTMLInterface::closeDiv();
        }

        FormUtils::placeHiddenField($this->fieldId, "", false);
        echo '<script>' . $this->getJSRefreshName() . "();" . '</script>';
        $this->moreJSRules();
        $this->onClickRule();
    }

    public function createChildTag($tag) : SpecialTag {
        return new SpecialTag($this->getChildID($tag['key']), $tag['key'], $tag['title'],
            $this, $this->getDefaultValue($tag['key']));
    }

    public function getDefaultValue($tagKey) : bool {
        return false;
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
