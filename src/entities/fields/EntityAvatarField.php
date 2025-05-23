<?php

class EntityAvatarField extends EntityField {
    protected CroppingImage | null $croppingImage = null;
    public bool $manualCrop = false;
    public int $width = 0;
    public int $height = 0;
    public bool $autoSubmit = false;

    public function __construct(string $path, public string $uniqueKey,
                                int $imageType = IMAGETYPE_JPEG){
        parent::__construct("avatar", "Avatar");
        $this->setAvatar($path, $imageType);
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

    public function getCroppingImage(SBEntity $entity, $record) : ?CroppingImage {
        $cid = $entity->setKey . "_" . $this->key;
        if($record instanceof SBEntityItem){
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

    public function presentCroppingImage(SBEntity $entity, $record){
        $niceDiv = new NiceDiv(8);
        $niceDiv->open();

        $croppingImage = $this->getCroppingImage($entity, $record);
        if($this->autoSubmit){
            $croppingImage->setAutoSubmitFormId($entity->getFormId());
        }

        if($this->width > 0) $croppingImage->presentFromWidth($this->width);
        else if($this->height > 0) $croppingImage->presentFromHeight($this->height);
        else $croppingImage->presentFromWidth(350);

        $niceDiv->close();
    }
}
