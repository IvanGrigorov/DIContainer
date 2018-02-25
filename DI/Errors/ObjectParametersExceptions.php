<?php 

namespace ObjectParametersExceptions;
use \Exception as Exception;

    final class MissingParametersException extends Exception {
        public function __construct() {
            parent::__construct("Missing parameters in injection config");
        }
    }

    final class MissingNameInParametersException extends Exception {
        public function __construct($paramName) {
            parent::__construct("Missing name: " .$paramName. " from the input config");
        }
    }

    final class MissingValueForParametersException extends Exception {
        public function __construct($paramName) {
            parent::__construct("Value for parameter: " .$paramName. " not given");
        }
    }
    