<?php

function slugify(string $str) : string {
    return strtolower(str_replace(" ", "_", $str));
}

function titlify(string $str) : string {
    return ucwords(str_replace("_", " ", $str));
}
