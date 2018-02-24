<?php 

namespace ObjectParametersExceptions;
use \Exception as Exception;

    final class MissingParametersException extends Exception {
        public function _construct() {
            parent::_construct("Missing parameters in injection config");
        }
    }

    final class MissingNameInParametersException extends Exception {
        public function _construct($paramName) {
            parent::_construct("Missing name: " .$paramName. " from the input config");
        }
    }

    final class MissingValueForParametersException extends Exception {
        public function _construct($paramName) {
            parent::_construct("Value for parameter: " .$paramName. " not given");
        }
    }
