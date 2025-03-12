<?php

class CroppingImage extends CroppableImage {
    public function handleSubmit($x, $y, $w, $h) : bool {
        try {
            $gumletImage = new \Gumlet\ImageResize($this->src);
            $orgFilename = new Filename($this->src);
            $copyFn = Routing::serverRootPath('.avnfiles/')
                . $orgFilename->pureName . '_' . time() . '.' . $orgFilename->extension;
            $gumletImage->freecrop($w, $h, $x, $y);

            copy($this->src, $copyFn);
            $gumletImage->save($this->src, $this->imageType);
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
