<?php
namespace Avetify\Themes\Modern;

class ModernGalleryMedal {
    public function __construct(public string $icon, public string $title,
                                public int $count, public string $link = ""){
    }
}