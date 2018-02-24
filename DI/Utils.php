<?php

require_once("Errors\GlobalExceptions.php");
require_once("Errors\WorkflowErrors.php");

use GlobalExceptions as CustomGlobalExceptions;
use WorkflowErrors as WorkflowErrors;


final class Utils {

    public function _construct() {

    }

    public function extractClassNameFromInterfaceName($interface) {
        if (!isset($interface)) {
            throw new \CustomGlobalExceptions\ParameterNotGIvenException("Parameters are missing or not passed to the function");
        }
        $replacesCount = 1;
        $className = str_replace("I", "_", $interface, $replacesCount); 
        if ($className === $interface) {
            throw new \WorkflowErrors\ConvertingInterfaceToClassNameErro($interface);
        }
        return $className;


    }
}