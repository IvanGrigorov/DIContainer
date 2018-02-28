<?php 
/**
 * File: GlobalExceptions.php
 * Project: PHPDI
 * File Created: Saturday, 17th February 2018 6:29:41 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Wednesday, 28th February 2018 11:51:53 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */

namespace GlobalExcpetions;
use \Exception as Exception;

    final class ParameterNotGIvenException extends Exception {

        //public function _construct($paramName) {
        //    parent::_construct("Parameter: ".$paramName." is missing or not passed to the function ");
        //}

        public function __construct() {
            parent::__construct("Parameter are missing or not passed to the function ");
        }
    }


