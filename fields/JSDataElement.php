<?php

abstract class JSDataElement implements Placeable {
    public function __construct(public string $dataSetKey, public array $records,
                                public string $primaryKey, public string $labelKey){}
}
