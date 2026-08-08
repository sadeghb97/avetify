<?php
namespace Avetify\Renderers;

use Avetify\Interface\PageRenderer;
use Avetify\Interface\Pout;

abstract class AdvancedTaskPage extends TaskPageRenderer {
    public int $errors = 0;

    public function doTask() : void {
        if(!$this->checkTaskState()) return;
        $this->mainTask();
        $this->finishTask();
    }

    public function checkTaskState() : bool {
        if($this->errors <= 0) return true;
        echo "You must fix " . $this->errors . " errors" . Pout::br();
        return false;
    }

    public function incrementErrors() : void {
        $this->errors++;
    }

    abstract public function mainTask() : void;

    abstract public function finishTask() : void;
}
