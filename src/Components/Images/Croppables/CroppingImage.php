<?php
namespace Avetify\Components\Images\Croppables;

use Avetify\Files\RecycleCan;
use Avetify\Models\Filename;
use function Avetify\Utils\br;

class CroppingImage extends CroppableImage {
    public function handleSubmit($x, $y, $w, $h) : bool {
        try {
            $gumletImage = new \Gumlet\ImageResize($this->serverSrc);
            $orgFilename = new Filename($this->serverSrc);
            $gumletImage->freecrop($w, $h, $x, $y);

            RecycleCan::saveBackupFile($this->id, $this->serverSrc);
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
