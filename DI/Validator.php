<?php

require_once("Errors\ObjectParametersExceptions.php");
require_once("Errors\GlobalExceptions.php");

use ObjectParametersExceptions as ObjectParamException; 
use GlobalExceptions as CustomGlobalExceptions;


final class Validator {

    public static function CheckForValidInjectionWithParameters($injectionConfig, $inputParams) {
        if (!isset($injectionConfig) || !isset($inputParams)) {
            throw new \CustomGlobalExceptions\ParameterNotGIvenException("Parameters are missing or not passed to the function");
        }
        if (!isset($injectionConfig["params"])) {
            throw new \ObjectParamException\MissingParametersException("Missing parameters in injection config");
        }
        foreach($injectionConfig["params"] as $key => $value) {
            $correctNameIsGiven = false;
            foreach($inputParams["params"] as $inputparam) {
                if ($key === $inputparam["name"]) {
                    if (!isset($inputparam["value"]) && !isset($value["defaultValue"])) {
                        throw new \ObjectParamException\MissingValueForParametersException("Value for parameter: " .$param["name"]. " not given");
                    }
                    $correctNameIsGiven = true;
                }
            } 
            if (!$correctNameIsGiven) {
                throw new \ObjectParamException\MissingNameInParametersException("Missing name: " .$param["name"]. " from the input config");
            }
        }
    }

    public static function checkCorrectParametersForInstantiation($paramConfig, $inputConfig) {
        $countOfConfigParams = count($paramConfig);
        $countedParams = 0;
        if (!($countOfConfigParams === count($inputConfig))) {
            // Make custom Exception
            throw new Exception("Invalid count of given parameters");
        }
        foreach($paramConfig as $param) {
            foreach($inputConfig["params"] as $inputParam) {
                if ($param->getName() === $inputParam["name"]) {
                    $countedParams++;
                }
            }
        }
        if ($countOfConfigParams === $countedParams) {
            return true;
        }
        return false;

    }
}