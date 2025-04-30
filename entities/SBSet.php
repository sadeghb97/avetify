<?php

class SBSet extends SetModifier {
    private array $map = [];
    public SetRenderer|null $renderer = null;

    public function __construct(array $set, public string $key = "set"){
        parent::__construct($this->key);
        $this->loadRawRecords($set);
        $this->renderer = $this->getRenderer();
    }

    public function loadFromFile($filename){
        if(file_exists($filename)){
            $tmpObject = json_decode(file_get_contents($filename), true);
            if(is_array($tmpObject)){
                $this->records = $tmpObject;
                $this->refreshMap();
            }
        }
    }

    public function loadRawRecords($rawRecords) {
        parent::loadRawRecords($rawRecords);
        $this->refreshMap();
    }

    public function refreshMap(){
        $this->map = [];
        foreach ($this->records as $index => $item){
            $this->map[$this->getItemId($item)] = $index;
        }
    }

    public function getItem($id){
        if(isset($this->map[$id])){
            return $this->records[$this->map[$id]];
        }
        return null;
    }

    public function finalSortFactors(): array {
        return [];
    }

    protected function getRenderer() : SetRenderer | null {
        return null;
    }

    public function renderPage(){
        $renderer = $this->getRenderer();
        if($renderer != null) $renderer->renderPage();
    }
}
