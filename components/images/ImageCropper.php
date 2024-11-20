<?php

class ImageCropper extends CroppableImage {
    public function __construct(string $src, string $id, float $ratio = 0, int $imageType = IMAGETYPE_JPEG,
                                public string $targetSrc = ""){
        parent::__construct($src, $id, $imageType, $ratio);
    }

    public function handleSubmit($x, $y, $w, $h){
        try {
            $gumletImage = new \Gumlet\ImageResize($this->src);
            $orgFilename = new Filename($this->src);
            $gumletImage->freecrop($w, $h, $x, $y);

            $gumletImage->save($this->targetSrc, $this->imageType);
            $this->onCropSuccess();
        } catch (\Gumlet\ImageResizeException $e) {
            $this->onCropError($e->getMessage());
        }
    }

    public function onCropError($error){
        echo 'Crop Error: ' . $error . br();
    }

    public function onCropSuccess(){
        //echo 'Image Cropped!' . br();
    }
}
