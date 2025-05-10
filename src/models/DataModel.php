<?php
class DataModel {
    public function __construct($data){
        $this->hydrate($data);
    }

    protected function hydrate(array $data): void {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
