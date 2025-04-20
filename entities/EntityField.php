<?php
class EntityField
{
    public ?string $key = null;
    public ?string $title = null;
    public ?string $type = null;
    public ?string $path = null;
    public ?string $targetExt = null;
    public ?string $maxImageSize = null;
    public int $forcedWidthDimension = 0;
    public int $forcedHeightDimension = 0;
    public bool $hidden = false;
    public bool $rtl = false;
    public bool $writable = false; // add field to edit and add forms
    public bool $printable = true; // print in forms
    public bool $required = false; // must have value in add and edit forms
    public bool $numeric = false;
    public bool $special = false; //ignore it on auto insert and update queries
    public bool $avatar = false; //ham special ham writable
    public bool $autoTimeCreate = false; //na special na writable
    public bool $autoTimeUpdate = false; //na special na writable
    //auto generated fields na special hastan na writable

    public function __construct($key, $title){
        $this->key = $key;
        $this->title = $title;
    }

    public function setType(string $type) : EntityField {
        $this->type = $type;
        return $this;
    }

    public function setPath(string $path) : EntityField {
        $this->path = $path;
        return $this;
    }

    public function setRtl() : EntityField {
        $this->rtl = true;
        return $this;
    }

    public function setHidden() : EntityField {
        $this->hidden = true;
        return $this;
    }


    public function setWritable() : EntityField {
        $this->writable = true;
        return $this;
    }

    public function notPrintable() : EntityField {
        $this->printable = false;
        return $this;
    }

    public function setWritableOnCreate() : EntityField {
        $this->writable = "create";
        return $this;
    }

    public function setRequired() : EntityField {
        $this->required = true;
        return $this;
    }

    public function setNumeric() : EntityField {
        $this->numeric = true;
        return $this;
    }

    public function setSpecial() : EntityField {
        $this->special = true;
        return $this;
    }

    public function setMaxImageSize(string $imageSize) : EntityField {
        $this->maxImageSize = $imageSize;
        return $this;
    }
    public function setImageForcedRatio(int $widthDim, int $heightDim) : EntityField {
        $this->forcedWidthDimension = $widthDim;
        $this->forcedHeightDimension = $heightDim;
        return $this;
    }

    public function setAvatar(string $path, string $extension = "jpg") : EntityField {
        $this->special = true;
        $this->writable = true;
        $this->avatar = true;
        $this->path = $path;
        $this->targetExt = $extension;
        return $this;
    }

    public function setAutoTimeCreate() : EntityField {
        $this->autoTimeCreate = true;
        return $this;
    }

    public function setAutoTimeUpdate() : EntityField {
        $this->autoTimeUpdate = true;
        return $this;
    }
}
