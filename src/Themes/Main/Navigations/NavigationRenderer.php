<?php
namespace Avetify\Themes\Main\Navigations;

use Avetify\Interface\Placeable;

abstract class NavigationRenderer implements Placeable {
    public function __construct(public NavigationBar $navigation) {}

    public function headImports(){}
    public function lateImports(){}
}