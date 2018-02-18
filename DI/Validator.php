<?php

include_once("Errors\ObjectParametersExceptions.php");
include_once("Errors\GlobalExceptions.php");

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
        foreach($injectionConfig["params"] as $param) {
            $correctNameIsGiven = false;
            foreach($inputParams["params"] as $inputparam) {
                if ($param["name"] === $inputparam["name"]) {
                    if (!isset($inputparam["value"]) && !isset($param["defaultValue"])) {
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
        $countOfAllConfigParams = count($paramConfig);
        $countedParams = 0;
        if (!($countOfAllConfigParams === count($inputConfig))) {
            // Make custom Exception
            throw new Exception("Invalid count of given parameters");
        }
        foreach($paramConfig as $param) {
            foreach($inputConfig as $inputParam) {
                if ($param->getName() === $inputConfig["name"]) {
                    $countedParams++;
                }
            }
        }
        if ($countOfAllConfigParams === $countedParams) {
            return true;
        }
        return false;

    }
}