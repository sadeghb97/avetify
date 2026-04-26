<?php
namespace Avetify\Modules;

use Avetify\Interface\Pout;
use Avetify\Routing\Routing;
use Avetify\Utils\TimeUtils\TimeUtils;

class PersistLogger {
    public function __construct(public string $logFilePath = ".logs") {
        if(!$this::checkLogFileExists()) return;
        $curScriptName = Routing::currentScriptName();
        $introLog = "******************** " . $curScriptName . ": Logging! ********************";
        file_put_contents($this->logFilePath, $introLog . "\n", FILE_APPEND);
    }

    public function log(string $message = "") : void {
        if(!$this::checkLogFileExists()) return;
        $ft = TimeUtils::formattedDateTime();
        $curScriptName = Routing::currentScriptName();
        $logLine = $ft . " - " . $curScriptName . ": " . $message;
        file_put_contents($this->logFilePath, $logLine . "\n", FILE_APPEND);
    }

    public function checkLogFileExists() : bool {
        if(!file_exists($this->logFilePath)){
            $line = "******************** Log file created! ********************";
            $res = file_put_contents($this->logFilePath, $line . "\n");
            if(!$res) {
                echo "The log file cannot be created in: " . $this->logFilePath . Pout::br();
                return false;
            }
        }
        return true;
    }
}
