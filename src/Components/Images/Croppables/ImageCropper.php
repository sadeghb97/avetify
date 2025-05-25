<?php
namespace Avetify\Components\Images\Croppables;

use Avetify\Externals\GumletImage\ImageResize;
use Avetify\Externals\GumletImage\ImageResizeException;
use Avetify\Interface\Pout;
use Avetify\Models\Filename;
use function Avetify\Utils\br;

class ImageCropper extends CroppableImage {
    public function __construct(string $serverSrc, string $id, float $targetRatio = 0, int $imageType = IMAGETYPE_JPEG,
                                public string $targetSrc = ""){
        parent::__construct($serverSrc, $id, $imageType, $targetRatio);
    }

    public function handleSubmit($x, $y, $w, $h) : bool {
        try {
            $gumletImage = new ImageResize($this->serverSrc);
            $orgFilename = new Filename($this->serverSrc);
            $gumletImage->freecrop($w, $h, $x, $y);

            $gumletImage->save($this->targetSrc, $this->imageType);
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
