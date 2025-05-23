<?php
namespace Avetify\Interface;

interface AvtContainer {
    public function open(WebModifier $webModifier = null);
    public function close();
    public function separate(WebModifier $webModifier = null);
}
