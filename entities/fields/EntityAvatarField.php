<?php

class EntityAvatarField extends EntityField {
    public function __construct(string $path, public string $uniqueKey, string $targetExt = "jpg"){
        parent::__construct("avatar", "Avatar");
        $this->setAvatar($path, $targetExt);
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
}
