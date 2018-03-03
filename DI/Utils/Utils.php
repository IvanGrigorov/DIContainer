<?php
/**
 * File: Utils.php
 * Project: PHPDI
 * File Created: Saturday, 24th February 2018 2:43:29 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Thursday, 1st March 2018 9:15:50 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */

define("FILE_LOCATION", dirname(__FILE__));

require_once(FILE_LOCATION."/../Errors/GlobalExceptions.php");
require_once(FILE_LOCATION."/../Errors/WorkflowErrors.php");

use GlobalExceptions as CustomGlobalExceptions;
use WorkflowErrors as WorkflowErrors;


final class Utils {

    public function __construct() {

    }
    

    public function extractClassNameFromInterfaceName($interface) {
        if (!isset($interface)) {
            throw new \CustomGlobalExceptions\ParameterNotGIvenException();
        }
        $replacesCount = 1;
        $className = str_replace("I", "_", $interface, $replacesCount); 
        if ($className === $interface) {
            throw new \WorkflowErrors\ConvertingInterfaceToClassNameException($interface);
        }
        return $className;


    }
}