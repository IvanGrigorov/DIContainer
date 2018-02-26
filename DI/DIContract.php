<?php
/**
 * File: DIContract.php
 * Project: DI
 * File Created: Saturday, 17th February 2018 3:12:18 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Sunday, 25th February 2018 10:31:53 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once ("Container.php");
require_once ("Validator.php");
require_once ("Utils.php");
require_once ("Log\Logger.php");
require_once ("Errors\GlobalExceptions.php");

use GlobalExceptions as CustomGlobalExceptions;

class DIContract {
    
    private static $self = null;
    private $mappedObject = array();
    private $mappedValueTypes = array();
    private $utilsMethodsClass;
    private $logger;

    private function __construct() {
        
    }
    public static function getInstance() {
        if (DIContract::$self === null) {
            DIContract::$self = new DIContract();
            DIContract::$self->mapInstances();
            DIContract::$self->mapValueTypes();
            DIContract::$self->mapParameterBasedObjects();
            DIContract::$self->utilsMethodsClass = new Utils();
            DIContract::$self->logger = new Logger();
            
        }
        return DIContract::$self;
    }
    
    private function mapInstances() {
        DIContract::$self->mappedObject["_URLParser"] = [
            "className" => "URLParser",
            "isSingleton" => true
        ];
        DIContract::$self->mappedObject["_RoutingMechanism"] = [
            "className" => "RoutingMechanism",
            "isSingleton" => true
        ];
        DIContract::$self->mappedObject["_ControllerRepository"] = [
            "className" => "ControllerRepository",
            "isSingleton" => true
        ];
        DIContract::$self->mappedObject["_View"] = [
            "className" => "GroundView",
            "isSingleton" => true
        ];
            
    
        //DIContract::$self->_URLParser = DIContainer::instatiateSingletonClass("URLParser");
        //DIContract::$self->_RoutingMechanism = DIContainer::instatiateSingletonClass("RoutingMechanism");
        //DIContract::$self->_ControllerRepository = DIContainer::instatiateSingletonClass("ControllerRepository");


    } 
    
    private function mapValueTypes() {
        DIContract::$self->mappedValueTypes[] = [
            "name" => "Name of Value",
            "type" => "string",
            "value" => "Test Value"
        ];
    }

    private function mapParameterBasedObjects() {
        DIContract::$self->mappedParameterBasedObjects["_URLParser"] = [
            "className" => "URLParser",
            "isSingleton" => true,
            "params" => array(
                "url" => 
                    [
                        "defaultValue" => "defaultvalue"
                    ]
            )
        ];
    }

    //private function mapInjectionHieararchyObjects() {
    //    DIContract::$self->mappedInjectionHieararchyObjects["_URLParser"] = [
    //        "className" => "URLParser",
    //        "isSingleton" => true,
    //        "params" => array(
    //            "url" => 
    //                [
    //                    "injectionMathod" => "defaultvalue",
    //                    "params"
    //                ]
    //        )
    //    ];
    //}
    
    public function getInjection($interface) {
        if (!interface_exists($interface)) {
            throw new Exception($interface . " is not declared in the required stack");
        }
        else {
            $className = DIContract::$self->utilsMethodsClass->extractClassNameFromInterfaceName($interface);
            if (!isset(DIContract::$self->mappedObject[$className])) {
                throw new Exception("GIven Inteface: ". $interface. " and extracted field name does not match the ones in the mapped Instances");
            }
            
            $instanceToInject = null;
            if (DIContract::$self->mappedObject[$className]["isSingleton"]) {
                $trace = debug_backtrace();
                DIContract::$self->checkAndTryToLog($trace, $className);        
                $instanceToInject = DIContainer::instatiateSingletonClass(DIContract::$self->mappedObject[$className]["className"]);
            }            
            else {
                $trace = debug_backtrace();
                DIContract::$self->checkAndTryToLog($trace, $className);        
                $instanceToInject = DIContainer::instantiateClass(DIContract::$self->mappedObject[$className]["className"]);
            } 
            if (!$instanceToInject instanceof $interface) {
                throw new Exception("Mapped class do not inherits the given Inteface: ". $interface);
            }
            return $instanceToInject;
        }
    }
    
    public function getInjectionwithScopeCheck($interface, $invocatorClassName) {
        $className = DIContract::$self->$utilsMethodsClass->extractClassNameFromInterfaceName($interface);
        $allowedInvocators = DIContract::$self->mappedObject[$className]["allowedInvocators"];
        if (!isset($allowedInvocators)) {
            throw new Exception("Cannot make check for allowed Invocators. allowedInvocators field is not set for ". DIContract::$self->mappedObject[$className]);
        }
        else {
            foreach ($allowedInvocators as $value) {
                if ($value === $invocatorClassName) {
                    $trace = debug_backtrace();
                    DIContract::$self->checkAndTryToLog($trace, $className);            
                    return $this->getInjection($interface);
                }
            }
            throw new Exception($invocatorClassName. " is not part of the allowed invocators for ". $className);

        }
    }
    
    // Make validator to check for duplicate name and types in mapped array
    public function getInjectedValueType($nameOfValueType, $typeOfValue) {
        foreach (DIContract::$self->mappedValueTypes as $valueType ) {
            if ($valueType["name"] === $nameOfValueType && 
                    $valueType["type"] === $typeOfValue) {
                DIContract::$self->checkAndTryToLog($trace, $valueType["name"]);            
                return $valueType["value"];
            }
        }
        throw new Exception("There is no mapped value with the given configuration: name: ".$nameOfValueType. ", type: ". $typeOfValue. " !");
        
    }

    public function getInjectionWithParams($interface, $params) {
        $className = DIContract::$self->utilsMethodsClass->extractClassNameFromInterfaceName($interface);
        $injectionConfig = DIContract::$self->mappedParameterBasedObjects[$className];
        try {
            Validator::CheckForValidInjectionWithParameters($injectionConfig, $params);
        }
        catch(Exception $e) {
            var_dump($e);
        }
        $trace = debug_backtrace();
        DIContract::$self->checkAndTryToLog($trace, $className);
        return DIContainer::instantiateObjectWithParameters($injectionConfig["className"], $params, $injectionConfig);
    }

    public function checkAndTryToLog($backtrace, $className) {
        if (Config::IS_LOGGING_ENABLED) {
            try {
                Validator::checkIfFileExists(Config::LOG_FILE_NAME);
                DIContract::$self->logger->log($backtrace, $className);
            }
            catch(\WorkflowErrors\FileNotFoundException $e) {
                var_dump($e->getMessage());
            }
        }
    }

    //public function getInjectionWithMappedInjectionParameters($interface, $params) {
    //    if(!isset($interface) || !isset($params)) {
    //        throw new \CustomGlobalExceptions\ParameterNotGIvenException("Parameters are missing or not passed to the function");
    //    }
        // Move class name extraction in method 
    //    $className = DIContract::$self->$utilsMethodsClass->extractClassNameFromInterfaceName($interface);
    //}
}
