<?php

function slugify(string $str) : string {
    return strtolower(str_replace(" ", "_", $str));
}
