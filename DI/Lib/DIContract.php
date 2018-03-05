<?php
/**
 * File: DIContract.php
 * Project: PHPDI
 * File Created: Saturday, 17th February 2018 3:12:18 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Monday, 5th March 2018 5:55:38 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */

require_once ("Container.php");
require_once (dirname(__FILE__)."/../Utils/Validator.php");
require_once (dirname(__FILE__)."/../Utils/Utils.php");
require_once (dirname(__FILE__)."/../Log/Logger.php");
require_once (dirname(__FILE__)."/../Log/ErrorLogger.php");
require_once (dirname(__FILE__)."/../Errors/GlobalExceptions.php");
require_once (dirname(__FILE__)."/../Errors/WorkFlowErrors.php");
require_once (dirname(__FILE__)."/../Errors/ObjectParametersExceptions.php");
require_once (dirname(__FILE__)."/Proxy/Proxy.php");



use GlobalExceptions;
use WorkflowErrors;
use ObjectParametersExceptions;



class DIContract {
    
    private static $self = null;
    private $mappedObject = array();
    private $mappedValueTypes = array();
    private $utilsMethodsClass;

    private function __construct() {
        
    }
    public static function getInstance() {
        if (DIContract::$self === null) {
            DIContract::$self = new DIContract();
            DIContract::$self->mapInstances();
            DIContract::$self->mapValueTypes();
            DIContract::$self->mapParameterBasedObjects();
            DIContract::$self->utilsMethodsClass = new Utils();
            
        }
        return DIContract::$self;
    }
    
    private function mapInstances() {
        DIContract::$self->mappedObject["_URLParser"] = [
            "className" => "URLParser",
            "isSingleton" => true,
            "lazy" => true
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
            "lazy" => true,
            "params" => array(
                "url" => 
                [
                        "defaultValue" => "defaultvalue"
                ]
            )
        ];
    }
    
    public function getInjection($interface) {
        if (!Validator::checkIfInterfaceIsLoaded($interface)) {
            throw new \WorkflowErrors\InterfaceNotLoadedException($interface);
        }
        else {
            $className = DIContract::$self->utilsMethodsClass->extractClassNameFromInterfaceName($interface);
            if (!isset(DIContract::$self->mappedObject[$className])) {
                throw new \GlobalExceptions\ParameterNotSetException($className);
            }
            
            $instanceToInject = null;
            if (!isset(DIContract::$self->mappedObject[$className]["isSingleton"])) {
                throw new \GlobalExceptions\ParameterNotSetException("isSingleton");
            }
            if (DIContract::$self->mappedObject[$className]["isSingleton"]) {
                $trace = debug_backtrace();
                Logger::getInstance()->tryLoggingInjection($trace, $className);
                $instanceToInject = DIContainer::instatiateSingletonClass(DIContract::$self->mappedObject[$className]["className"], 
                    DIContract::$self->mappedObject[$className]["lazy"]);
            }            
            else {
                $trace = debug_backtrace();
                Logger::getInstance()->tryLoggingInjection($trace, $className);
                $instanceToInject = DIContainer::instantiateClass(DIContract::$self->mappedObject[$className]["className"],
                DIContract::$self->mappedObject[$className]["lazy"]);
            } 
            if (!$instanceToInject instanceof $interface && Config::CHECK_FOR_INTERFACE) {
                throw new \WorkflowErrors\InterfaceNotInheritedException($interface);
            }
            return $instanceToInject;
        }
    }
    
    public function getInjectionwithScopeCheck($interface, $invocatorClassName) {
        if (!Validator::checkIfInterfaceIsLoaded($interface)) {
            throw new \WorkflowErrors\InterfaceNotLoadedException($interface);
        }
        $className = DIContract::$self->$utilsMethodsClass->extractClassNameFromInterfaceName($interface);
        if (!isset(DIContract::$self->mappedObject[$className])) {
            throw new \GlobalExceptions\ParameterNotSetException($className);
        }
        if (!isset(DIContract::$self->mappedObject[$className]["allowedInvocators"])) {
            throw new \GlobalExceptions\ParameterNotSetException('allowedInvocators');
        }
        $allowedInvocators = DIContract::$self->mappedObject[$className]["allowedInvocators"];
        if (!isset($allowedInvocators)) {
            throw new Exception("Cannot make check for allowed Invocators. allowedInvocators field is not set for ". DIContract::$self->mappedObject[$className]);
        }
        else {
            foreach ($allowedInvocators as $value) {
                if ($value === $invocatorClassName) {
                    $trace = debug_backtrace();
                    Logger::getInstance()->tryLoggingInjection($trace, $className);
                    $instanceToInject = $this->getInjection($interface);
                    if (!$instanceToInject instanceof $interface && Config::CHECK_FOR_INTERFACE) {
                        throw new \WorkflowErrors\InterfaceNotInheritedException($interface);
                    }
                    return $instanceToInject;
        
                }
            }
            throw new \ObjectParametersExceptions\InvocatorNotAllowedException($invocatorClassName, $className);

        }
    }
    
    // Make validator to check for duplicate name and types in mapped array
    public function getInjectedValueType($nameOfValueType, $typeOfValue) {
        foreach (DIContract::$self->mappedValueTypes as $valueType ) {
            if ($valueType["name"] === $nameOfValueType && 
                    $valueType["type"] === $typeOfValue) {
                Logger::getInstance()->tryLoggingInjection($trace, $valueType["name"]);            
                return $valueType["value"];
            }
        }
        throw new Exception("There is no mapped value with the given configuration: name: ".$nameOfValueType. ", type: ". $typeOfValue. " !");
        
    }

    public function getInjectionWithParams($interface, $params) {
        if (!Validator::checkIfInterfaceIsLoaded($interface)) {
            throw new \WorkflowErrors\InterfaceNotLoadedException($interface);
        }
        $className = DIContract::$self->utilsMethodsClass->extractClassNameFromInterfaceName($interface);
        if (!isset(DIContract::$self->mappedParameterBasedObjects[$className])) {
            throw new \GlobalExceptions\ParameterNotSetException($className);
        }
        $injectionConfig = DIContract::$self->mappedParameterBasedObjects[$className];
        try {
            Validator::CheckForValidInjectionWithParameters($injectionConfig, $params);
        }
        catch(Exception $e) {
            ErrorLogger::getInstance()->tryLoggingError($e);
        }
        $trace = debug_backtrace();
        Logger::getInstance()->tryLoggingInjection($trace, $className);
        $instanceToInject = null;
        if (!isset(DIContract::$self->mappedObject[$className]["isSingleton"])) {
            throw new \GlobalExceptions\ParameterNotSetException("isSingleton");
        }
        if (DIContract::$self->mappedParameterBasedObjects[$className]["isSingleton"]) {
            $instanceToInject = DIContainer::instantiateSingletonObjectWithParameters($injectionConfig["className"], $params, $injectionConfig);
        }
        else {
            $instanceToInject = DIContainer::instantiateObjectWithParameters($injectionConfig["className"], $params, $injectionConfig);

        }
        if (!$instanceToInject instanceof $interface && Config::CHECK_FOR_INTERFACE) {
            throw new \WorkflowErrors\InterfaceNotInheritedException($interface);
        }
        return $instanceToInject;
    }
}
