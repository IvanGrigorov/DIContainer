<?php
/**
 * File: Container.php
 * Project: PHPDI
 * File Created: Saturday, 17th February 2018 3:12:18 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Saturday, 3rd March 2018 9:49:31 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//define("FILE_LOCATION", dirname(__FILE__));

 require_once(dirname(__FILE__)."/../AutoLoader/AutoLoader.php");
 require_once(dirname(__FILE__)."/../AutoLoader/LoaderConfig.php");
 require_once(dirname(__FILE__)."/../Errors/WorkflowErrors.php");

 use WorkflowErrors as WorkflowErrors;
 class DIContainer {
    
    private static $self = null; 
    private $loader;
    private function __construct() {
        $this->loader = new AutoLoader();
    }
    
    
    private static function getInstance() {
        if (DIContainer::$self === null) {
            DIContainer::$self = new DIContainer();
        }
        return DIContainer::$self;
    }
    
    public static function instantiateClass($class) {
        if (!Validator::checkIfConstantIsDefined(constant("LoaderConfig::".strtoupper($class)))) {
            $exception = new  \WorkflowErrors\ConstantNotDefinedException($constantName);
            throw $exception;

        }
        DIContainer::getInstance()->loader->load(constant("LoaderConfig::".strtoupper($class)));
        return new $class();
    }
    
    public static function instatiateSingletonClass($class) {
        if (!Validator::checkIfConstantIsDefined(constant("LoaderConfig::".strtoupper($class)))) {
            $exception = new  \WorkflowErrors\ConstantNotDefinedException($constantName);
            throw $exception;

        }
        DIContainer::getInstance()->loader->load(constant("LoaderConfig::".strtoupper($class)));
        $fieldName = "_".$class;
        if (!isset(DIContainer::getInstance()->$fieldName)) {
            DIContainer::getInstance()->$fieldName = new $class();
        }
        return DIContainer::getInstance()->$fieldName;
    }
    
    public static function clearSingletonObject($class) {
        $fieldName = "_".$class;
        if (isset(DIContainer::getInstance()->$fieldName)) {
            DIContainer::getInstance()->$fieldName = null;
            unset(DIContainer::getInstance()->$fieldName);
        }
    }
    
    public static function returnValueType() {
        
    }

    public static function instantiateObjectWithParameters($class, $parameters, $injectionConfig) {
        if (!Validator::checkIfConstantIsDefined(constant("LoaderConfig::".strtoupper($class)))) {
            $exception = new  \WorkflowErrors\ConstantNotDefinedException($constantName);
            throw $exception;

        }
        DIContainer::getInstance()->loader->load(constant("LoaderConfig::".strtoupper($class)));
        $reflectionClass = new ReflectionClass($class);
        $reflectionConstructor = $reflectionClass->getConstructor();
        $reflectionConstructorParams = $reflectionConstructor->getParameters();
        if (Validator::checkCorrectParametersForInstantiation($reflectionConstructorParams, $parameters)) {
            $setParams = [];
            foreach($reflectionConstructorParams as $reflectionParam) {
                foreach($parameters["params"] as $key => $value) {
                    if ($reflectionParam->getName() === $value["name"]) {
                        if (!isset($value["value"])) {
                            $setParams[] = $injectionConfig["params"][$value["name"]]["defaultValue"];            
                        }
                        else {
                            $setParams[] = $value["value"];
                        }
                    }
                }
            }
            return $reflectionClass->newInstanceArgs($setParams);
        }
        throw new Exception("Wrong param config");
    }


    public static function instantiateSingletonObjectWithParameters($class, $parameters, $injectionConfig) {
        if (!Validator::checkIfConstantIsDefined(constant("LoaderConfig::".strtoupper($class)))) {
            $exception = new  \WorkflowErrors\ConstantNotDefinedException($constantName);
            throw $exception;

        }
        DIContainer::getInstance()->loader->load(constant("LoaderConfig::".strtoupper($class)));
        $fieldName = "_".$class;
        if (!isset(DIContainer::getInstance()->$fieldName)) {
            DIContainer::getInstance()->$fieldName = DIContainer::getInstance()->instantiateObjectWithParameters($class, $parameters, $injectionConfig);
        }
        return DIContainer::getInstance()->$fieldName;
    }

    // Add scope check if the instance is called from corect file
}