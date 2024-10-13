<?php

class CroppingImage extends CroppableImage {
    public function handleSubmit($x, $y, $w, $h){
        try {
            $gumletImage = new \Gumlet\ImageResize($this->src);
            $orgFilename = new Filename($this->src);
            $copyFn = Routing::serverRootPath('.avnfiles/')
                . $orgFilename->pureName . '_' . time() . '.' . $orgFilename->extension;
            $gumletImage->freecrop($w, $h, $x, $y);

            copy($this->src, $copyFn);
            $gumletImage->save($this->src, IMAGETYPE_JPEG);
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
