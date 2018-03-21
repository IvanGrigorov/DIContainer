<?php
/*
 * File: ADIContract.php
 * Project: PHPDI
 * File Created: Wednesday, 21st March 2018 10:16:37 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Wednesday, 21st March 2018 10:47:55 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */

 require_once(dirname(__FILE__)."/IDIContract.PHP");
 require_once (dirname(__FILE__)."/../Container.php");
require_once (dirname(__FILE__)."/../../Utils/Validator.php");
require_once (dirname(__FILE__)."/../../Utils/Utils.php");
require_once (dirname(__FILE__)."/../../Log/Logger.php");
require_once (dirname(__FILE__)."/../../Log/ErrorLogger.php");
require_once (dirname(__FILE__)."/../../Errors/GlobalExceptions.php");
require_once (dirname(__FILE__)."/../../Errors/WorkflowErrors.php");
require_once (dirname(__FILE__)."/../../Errors/ObjectParametersExceptions.php");
require_once (dirname(__FILE__)."/..//Proxy/Proxy.php");

use GlobalExceptions;
use WorkflowErrors;
use ObjectParametersExceptions;

abstract class ADIContract implements IDIContract{

    
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