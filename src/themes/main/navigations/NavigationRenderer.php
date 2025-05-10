<?php

abstract class NavigationRenderer implements Placeable {
    public function __construct(public NavigationBar $navigation) {}
}