<?php 

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


