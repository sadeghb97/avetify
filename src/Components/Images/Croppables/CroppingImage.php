<?php
namespace Avetify\Components\Images\Croppables;

use Avetify\Externals\GumletImage\ImageResize;
use Avetify\Externals\GumletImage\ImageResizeException;
use Avetify\Files\RecycleCan;
use Avetify\Interface\Pout;
use Avetify\Models\Filename;
use Avetify\Modules\Printer;

class CroppingImage extends CroppableImage {
    public function handleSubmit($x, $y, $w, $h) : bool {
        try {
            $gumletImage = new ImageResize($this->serverSrc);
            $orgFilename = new Filename($this->serverSrc);
            $gumletImage->freecrop($w, $h, $x, $y);

            RecycleCan::saveBackupFile($this->id, $this->serverSrc);
            $gumletImage->save($this->serverSrc, $this->imageType);
            $this->onCropSuccess();
            return true;
        } catch (ImageResizeException $e) {
            $this->onCropError($e->getMessage());
            return false;
        }
    }

    public function onCropError($error){
        echo 'Crop Error: ' . $error . Pout::br();
    }

    public function onCropSuccess(){
        //echo 'Image Cropped!' . Pout::br();
    }
}
