<?php

interface ValueGetter {
    public function getValue($item) : string | float;
}
