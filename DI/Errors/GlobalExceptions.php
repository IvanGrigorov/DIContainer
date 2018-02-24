<?php 

namespace GlobalExcpetions;
use \Exception as Exception;

    final class ParameterNotGIvenException extends Exception {

        //public function _construct($paramName) {
        //    parent::_construct("Parameter: ".$paramName." is missing or not passed to the function ");
        //}

        public function _construct() {
            parent::_construct("Parameter are missing or not passed to the function ");
        }
    }


