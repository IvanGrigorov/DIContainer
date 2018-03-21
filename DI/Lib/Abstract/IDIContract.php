<?php
/*
 * File: IDIContract
 * Project: PHPDI
 * File Created: Wednesday, 21st March 2018 10:09:12 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Wednesday, 21st March 2018 10:36:47 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */

 interface IDIContract {
     
    static function getInstance();
    function mapInstances();
    function mapValueTypes();
    function mapParameterBasedObjects();
    function getInjection($interface);
    function getInjectionwithScopeCheck($interface, $invocatorClassName);
    function getInjectedValueType($nameOfValueType, $typeOfValue);
    function getInjectionWithParams($interface, $params);

}