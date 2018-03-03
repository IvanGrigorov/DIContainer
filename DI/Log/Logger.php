<?php 
/**
 * File: Logger.php
 * Project: PHPDI
 * File Created: Sunday, 25th February 2018 7:12:16 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Saturday, 3rd March 2018 9:50:29 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */

//define("FILE_LOCATION", dirname(__FILE__));
require_once(dirname(__FILE__)."/../Lib/Config.php");

final class Logger {

    private $filePathToLogInstantiations;

    public function __construct() {
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