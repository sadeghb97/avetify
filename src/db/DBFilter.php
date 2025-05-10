<?php

class DBFilter {
    public function __construct(public string $key, public string $operator,
                                public string $value, public bool $isNumeric = false){
    }
}
