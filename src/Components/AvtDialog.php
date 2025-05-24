<?php
namespace Avetify\Components;

use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;

abstract class AvtDialog {
    public function __construct(public string $key,
                                public string $title,
                                public string $jsSubmitName){}

    protected function jsInit(){
        ?>
        <script>
            var <?php echo $this->jsParamsVarName(); ?> = {};
            function <?php echo $this->_inflateDialogName(); ?>(constParams){
                <?php echo $this->jsParamsVarName(); ?> = constParams
                const viewport = document.getElementById('avtDialogViewport');
                const template = document.getElementById('<?php echo $this->getTemplateId(); ?>');

                viewport.innerHTML = '';
                const clone = document.importNode(template.content, true);
                viewport.appendChild(clone);
                document.getElementById('avtDialogOverlay').style.display = 'flex';
            }

            function <?php echo $this->mainSubmitFunctionName(); ?>(constParams){
                const viewport = document.getElementById('avtDialogViewport');
                const dataElements = viewport.querySelectorAll('.dialog-data');
                const formData = {};

                dataElements.forEach(el => {
                    formData[el.id] = el.value
                });

                <?php echo $this->jsSubmitName ?>('<?php echo $this->key ?>', constParams, formData);
            }
        </script>
        <?php
    }

    public function jsInflateDialog($params) : string {
        $cmdJson = json_encode($params);
        $cmdSafe = htmlspecialchars($cmdJson, ENT_QUOTES, 'UTF-8');
        return $this->_inflateDialogName() . '(' . $cmdSafe . ')';
    }

    private function _inflateDialogName() : string {
        return "inflate_dialog_" . $this->key;
    }

    public function mainSubmitFunctionName() : string {
        return "submit_dialog_" . $this->key;
    }

    private function jsParamsVarName() : string {
        return "params_" . $this->key;
    }

    private function jsSuccessMessageId() : string {
        return "success_message_" . $this->key;
    }

    private function jsErrorMessageId() : string {
        return "error_message_" . $this->key;
    }

    private function getTemplateId() : string {
        return "template_" . $this->key;
    }

    public static function hideDialogName() : string {
        return "closeAvtDialog";
    }

    public static function addLabelField(string $target){
        echo '<label ';
        HTMLInterface::addAttribute("for", $target);
        HTMLInterface::closeTag();
        echo '</label>';
    }

    public static function addNumberField(string $id, bool $isData, bool $isRequired = false){
        echo '<input ';
        HTMLInterface::addAttribute("type", "number");
        HTMLInterface::addAttribute("id", $id);
        HTMLInterface::addAttribute("name", $id);
        Styler::classStartAttribute();
        if($isData) Styler::addClass("dialog-data");
        Styler::closeAttribute();
        if($isRequired) HTMLInterface::addAttribute("required");
        HTMLInterface::closeSingleTag();
    }

    public static function addTextAreaField(string $id, bool $isData, bool $isRequired = false){
        echo '<textarea ';
        HTMLInterface::addAttribute("id", $id);
        HTMLInterface::addAttribute("name", $id);
        HTMLInterface::addAttribute("rows", 4);
        Styler::classStartAttribute();
        if($isData) Styler::addClass("dialog-data");
        Styler::closeAttribute();
        if($isRequired) HTMLInterface::addAttribute("required");
        HTMLInterface::closeTag();
        echo '</textarea>';
    }

    public function addPrimarySubmit(){
        echo '<button class="primary-button" '
            . 'onclick="' . $this->mainSubmitFunctionName()
            . '(' . $this->jsParamsVarName() . ');">Submit</button>';
    }

    public function addPrimaryTitle(){
        echo '<div class="avt-dialog-title">' . $this->title . '</div>';
    }

    public function addPrimarySuccessMessage(){
        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("avt-dialog-success-message");
        Styler::closeAttribute();
        Styler::startAttribute();
        Styler::addStyle("display", "none");
        Styler::closeAttribute();
        HTMLInterface::addAttribute("id", $this->jsSuccessMessageId());
        HTMLInterface::closeTag();
        HTMLInterface::closeDiv();
    }

    public function addPrimaryErrorMessage(){
        echo '<div ';
        Styler::classStartAttribute();
        Styler::addClass("avt-dialog-error-message");
        Styler::closeAttribute();
        Styler::startAttribute();
        Styler::addStyle("display", "none");
        Styler::closeAttribute();
        HTMLInterface::addAttribute("id", $this->jsErrorMessageId());
        HTMLInterface::closeTag();
        HTMLInterface::closeDiv();
    }

    protected static function placeDialogHeader(){
        echo '<div class="avt-dialog-overlay" id="avtDialogOverlay">
            <div class="avt-dialog-box" id="avtDialogBox">';
        echo '<button class="avt-dialog-close-button" '
            . 'onclick="' . self::hideDialogName() . '()">×</button>';
        echo '<div id="avtDialogViewport"></div>';
    }

    protected static function placeDialogFooter(){
        echo '</div></div>';
    }

    public static function place(){
        self::placeDialogHeader();
        self::placeDialogFooter();
    }

    protected function placeTemplate(){
        echo '<template id="' . $this->getTemplateId() . '">';
        $this->placeTemplateContents();
        echo '</template>';
    }

    abstract protected function placeTemplateContents();

    public function prepare(){
        $this->jsInit();
        $this->placeTemplate();
    }
}
