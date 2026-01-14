<?php
namespace Avetify\Entities\Fields;

use Avetify\Components\Containers\NiceDiv;
use Avetify\Components\Images\Croppables\CroppingImage;
use Avetify\Entities\AvtEntity;
use Avetify\Entities\AvtEntityItem;
use Avetify\Entities\EntityField;
use Avetify\Entities\EntityUtils;
use Avetify\Files\ImageUtils;
use Avetify\Interface\HTMLInterface;
use Avetify\Interface\Styler;
use Avetify\Interface\WebModifier;
use Avetify\Routing\Routing;

class EntityAvatarField extends EntityField {
    protected CroppingImage | null $croppingImage = null;
    public ?string $path = null;
    public ?int $targetImageType = null;
    public ?string $targetExt = null;
    public ?string $maxImageSize = null;
    public int $forcedWidthDimension = 0;
    public int $forcedHeightDimension = 0;
    public bool $manualCrop = false;
    public int $width = 0;
    public int $height = 0;
    public bool $autoSubmit = false;

    public function __construct(string $path, public string $uniqueKey,
                                public AvtEntity $avtEntity, int $imageType = IMAGETYPE_JPEG){
        parent::__construct("avatar", "Avatar");

        $this->special = true;
        $this->writable = true;
        $this->path = $path;
        $this->targetImageType = $imageType;
        $this->targetExt = ImageUtils::getImageExtension($imageType);
    }

    private function noExtRelativeSrc($record) : string {
        return $this->path .
            EntityUtils::getSimpleValue($record, $this->uniqueKey);
    }

    private function getRelativeSrc($record) : string {
        return $this->noExtRelativeSrc($record) . "." . $this->targetExt;
    }

    public function noExtBrowserSrc($record) : string {
        return Routing::browserRootPath($this->noExtRelativeSrc($record));
    }

    public function noExtServerSrc($record) : string {
        return Routing::serverRootPath($this->noExtRelativeSrc($record));
    }

    public function getBrowserSrc($record) : string {
        return Routing::browserRootPath($this->getRelativeSrc($record));
    }

    public function getServerSrc($record) : string {
        return Routing::serverRootPath($this->getRelativeSrc($record));
    }

    public function setManualCrop() : EntityAvatarField {
        $this->manualCrop = true;
        return $this;
    }

    public function setAutoSubmit() : EntityAvatarField {
        $this->autoSubmit = true;
        return $this;
    }

    public function getCroppingImage($record) : ?CroppingImage {
        $cid = $this->avtEntity->setKey . "_" . $this->key;
        if($record instanceof AvtEntityItem){
            $cid .= ("_" . $record->getItemId());
        }
        $serverSrc = $this->getServerSrc($record);

        if($this->croppingImage == null && file_exists($serverSrc)){
            $this->croppingImage = new CroppingImage($serverSrc,
                $cid, $this->targetImageType,
                $this->getImageForcedRatio());
        }
        return $this->croppingImage;
    }

    public function presentCroppingImage($record){
        $niceDiv = new NiceDiv(8);
        $niceDiv->open();

        $croppingImage = $this->getCroppingImage($record);
        if($this->autoSubmit){
            $croppingImage->setAutoSubmitFormId($this->avtEntity->getFormId());
        }

        if($this->width > 0) $croppingImage->presentFromWidth($this->width);
        else if($this->height > 0) $croppingImage->presentFromHeight($this->height);
        else $croppingImage->presentFromWidth(350);

        $niceDiv->close();
    }

    public function presentWritableField($item, ?WebModifier $webModifier = null) {
        $title = $this->title;
        $key = $this->key;

        $avExists = false;
        $avBrowserSrc = "";
        $avServerSrc = "";

        if($item){
            $avServerSrc = $this->getServerSrc($item);
            $avBrowserSrc = $this->getBrowserSrc($item);
            if(file_exists($avServerSrc)){
                $avExists = true;
            }
        }

        if($avExists && $this->manualCrop){
            $this->presentCroppingImage($item);
        }

        $div = new NiceDiv(12);
        $div->addStyle("margin-top", "8px");
        $div->addStyle("margin-bottom", "8px");
        $div->open();

        if(!$avExists) {
            HTMLInterface::placeText("$title: ");
            $div->separate();
        }

        echo '<input ';
        HTMLInterface::addAttribute("type", "file");
        $this->placeElementIdAttributes();
        HTMLInterface::addAttribute("class", "empty");
        Styler::startAttribute();
        Styler::addStyle("font-size", "13pt");
        Styler::closeAttribute();
        HTMLInterface::closeSingleTag();

        HTMLInterface::placePostInput($key, "", $title . " Url");

        if($avExists && !$this->manualCrop){
            $avatarModifier = WebModifier::createInstance();
            $avatarModifier->styler->pushStyle("margin-bottom", "8px");
            HTMLInterface::placeImageWithHeight($avBrowserSrc . "?" . time(), 120,
                $avatarModifier);
            $div->separate();
        }

        $div->close();
    }
}
