<?php
namespace Avetify\Components\Images\Croppables;

use Avetify\Externals\GumletImage\ImageResize;
use Avetify\Files\ImageUtils;
use Avetify\Files\RecycleCan;
use Avetify\Interface\Pout;
use Avetify\Models\Filename;
use Exception;

class CroppingImage extends CroppableImage {
    public function handleSubmit($x, $y, $w, $h) : bool {
        try {
            RecycleCan::saveBackupFile($this->id, $this->serverSrc);

            if(!$this->magickMode) {
                $gumletImage = new ImageResize($this->serverSrc);
                $orgFilename = new Filename($this->serverSrc);
                $gumletImage->freecrop($w, $h, $x, $y);
                $gumletImage->save($this->serverSrc, $this->imageType);
            }
            else {
                ImageUtils::magickCrop($this->serverSrc, $w, $h, $x, $y);
            }

            $this->onCropSuccess();
            return true;
        } catch (Exception $e) {
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
