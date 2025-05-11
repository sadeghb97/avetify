<?php

interface HaveTitle {
    public function getItemTitle() : string;
}

interface HaveID {
    public function getItemId() : string;
}

interface HaveImage {
    public function getItemImage() : string;
}

interface HaveLink {
    public function getItemLink() : string;
}

interface HaveAltLink {
    public function getItemAltLink() : string;
}

interface EntityProfile extends HaveID, HaveTitle, HaveImage, HaveLink, HaveAltLink {}

interface HaveImageRatio {
    public function getItemRatio() : float;
}

interface EntityTitle {
    public function getItemTitle($record) : string;
}

interface EntityID {
    public function getItemId($record) : string;
}

interface EntityImage {
    public function getItemImage($record) : string;
}

interface EntityLink {
    public function getItemLink($record) : string;
}

interface EntityAltLink {
    public function getItemAltLink($record) : string;
}

interface EntityManager extends EntityID, EntityTitle, EntityImage, EntityLink, EntityAltLink {}

interface EntityImageRatio {
    public function getItemRatio($record) : float;
}
