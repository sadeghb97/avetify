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
        file_put_contents('file.txt'. $introLog . "\n", FILE_APPEND);
    }

    public function log(string $message = "") : void {
        if(!$this::checkLogFileExists()) return;
        $ft = TimeUtils::formattedDateTime();
        $curScriptName = Routing::currentScriptName();
        $logLine = $ft . " - " . $curScriptName . ": " . $message;
        file_put_contents('file.txt'. $logLine . "\n", FILE_APPEND);
    }

    public function checkLogFileExists() : bool {
        if(!file_exists($this->logFilePath)){
            echo "Log File Not Found In: " . $this->logFilePath . Pout::br();
            return false;
        }
        return true;
    }
}
