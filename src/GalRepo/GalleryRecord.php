<?php
namespace Avetify\GalRepo;

class GalleryRecord {
    public function __construct(public int $galleryIndex, public string $path, public int $imageIndex){}
}