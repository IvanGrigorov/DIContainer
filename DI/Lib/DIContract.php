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

require_once(dirname(__FILE__)."/Abstract/IDIContract.php");
require_once(dirname(__FILE__)."/Abstract/ADIContract.php");


class DIContract extends ADIContract implements IDIContract{
    
    protected static $self = null;
    protected $mappedObject = array();
    protected $mappedValueTypes = array();
    protected $utilsMethodsClass;

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
 
    public function mapInstances() {
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
    
    public function mapValueTypes() {
        DIContract::$self->mappedValueTypes[] = [
            "name" => "Name of Value",
            "type" => "string",
            "value" => "Test Value"
        ];
    }

    public function mapParameterBasedObjects() {
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
}
