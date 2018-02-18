<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DIContainer {
    
    private static $self = null; 
    
    private function _construct() {
    }
    
    private static function getInstance() {
        if (DIContainer::$self === null) {
            DIContainer::$self = new DIContainer();
        }
        return DIContainer::$self;
    }
    
    public static function instantiateClass($class) {
        return new $class();
    }
    
    public static function instatiateSingletonClass($class) {
        $fieldName = "_".$class;
        if (!isset(DIContainer::getInstance()->$fieldName)) {
            DIContainer::getInstance()->$fieldName = new $class();
        }
        return DIContainer::getInstance()->$fieldName;
    }
    
    public static function clearSingletonObject($class) {
        $fieldName = "_".$class;
        if (!isset(DIContainer::getInstance()->$fieldName)) {
            DIContainer::getInstance()->$fieldName = null;
            unset(DIContainer::getInstance()->$fieldName);
        }
    }
    
    public static function returnValueType() {
        
    }

    public static function instantiateObjectWithParameters($class, $parameters, $injectionConfig) {
        $reflectionClass = new ReflectionClass($class);
        $reflectionConstructor = $reflectionClass->getConstructor();
        $reflectionConstructorParams = $reflectionConstructor->getParameters();
        if (Validator::checkCorrectParametersForInstantiation($reflectionConstructorParams, $parameters)) {
            $setParams = [];
            foreach($reflectionConstructorParams as $reflectionParam) {
                foreach($parameters["params"] as $inputparam) {
                    if ($reflectionParam->getName() === $inputparam["name"]) {
                        if (!isset($inputparam["value"])) {
                            foreach($injectionConfig["params"] as $defaultParams) {
                                if ($defaultParams["name"] === $inputparam["name"]) {
                                    $setParams[] = $defaultParams["defaultValue"];
                                }
                            }
                        }
                        else {
                            $setParams[] = $inputparam["value"];
                        }
                    }
                }
            }
            return $reflectionClass->newInstanceArgs($setParams);
        }
        throw new Exception("Wrong param config");
    }
    
    // Add scope check if the instance is called from corect file
}