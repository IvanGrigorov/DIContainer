<?php 
/**
 * File: WorkflowErrors.php
 * Project: PHPDI
 * File Created: Sunday, 25th February 2018 7:12:16 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Wednesday, 28th February 2018 11:52:06 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */

namespace WorkflowErrors;
use \Exception as Exception;

    final class ConvertingInterfaceToClassNameException extends Exception {

        public function __construct($interface) {
            parent::__construct("Interface name is not correctly given: ".$interface);
        }
    }

    final class FileNotFoundException extends Exception {

        public function __construct($filepath) {
            parent::__construct("File not found: ".$filepath);
        }
    }

    final class ConstantNotDefinedException extends Exception {

        public function __construct($constantName) {
            parent::__construct("Constant not defined: ".$constantName);
        }
    }

