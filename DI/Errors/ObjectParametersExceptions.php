<?php 
/**
 * File: ObjectParametersExceptions.php
 * Project: Errors
 * File Created: Saturday, 17th February 2018 6:12:12 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Sunday, 25th February 2018 10:31:03 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */

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
    