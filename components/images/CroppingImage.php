<?php

class CroppingImage extends CroppableImage {
    public function handleSubmit($x, $y, $w, $h) : bool {
        try {
            $gumletImage = new \Gumlet\ImageResize($this->serverSrc);
            $orgFilename = new Filename($this->serverSrc);
            $copyFn = Routing::serverRootPath('.avtfiles/')
                . $orgFilename->pureName . '_' . time() . '.' . $orgFilename->extension;
            $gumletImage->freecrop($w, $h, $x, $y);

            copy($this->serverSrc, $copyFn);
            $gumletImage->save($this->serverSrc, $this->imageType);
            $this->onCropSuccess();
            return true;
        } catch (\Gumlet\ImageResizeException $e) {
            $this->onCropError($e->getMessage());
            return false;
        }
    }

    public function onCropError($error){
        echo 'Crop Error: ' . $error . br();
    }

    public function onCropSuccess(){
        //echo 'Image Cropped!' . br();
    }
}
