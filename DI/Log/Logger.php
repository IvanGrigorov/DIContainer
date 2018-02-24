<?php 

require_once(dirname(__FILE__)."/../Config.php");

final class Logger {

    private $filePathToLogInstantiations;

    public function _construct() {
        $this->filePathToLogInstantiations = Config::LOG_FILE_NAME;
    }

    public function log($backtrace, $injection) {
        file_put_contents(Config::LOG_FILE_NAME, "==================== \r\n", FILE_APPEND); 
        file_put_contents(Config::LOG_FILE_NAME, "file: ".$backtrace[0]["file"]. " \r\n", FILE_APPEND); 
        file_put_contents(Config::LOG_FILE_NAME, "line: ".$backtrace[0]["line"]. " \r\n", FILE_APPEND); 
        file_put_contents(Config::LOG_FILE_NAME, "injection: ".$injection. " \r\n", FILE_APPEND); 
        file_put_contents(Config::LOG_FILE_NAME, "==================== \r\n", FILE_APPEND); 

    }
}