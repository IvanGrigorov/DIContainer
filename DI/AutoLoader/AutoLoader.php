<?php
/**
 * File: AutoLoader.php
 * Project: PHPDI
 * File Created: Wednesday, 28th February 2018 9:44:41 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Wednesday, 28th February 2018 10:31:43 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */
//require_once("AutoLoader/LoaderConfig.php");

 final class AutoLoader {
     
    public function __construct() {

    }

    public function load($path) {
        try { 
            require_once($path);
        }   
        catch (Exception $e) {
            var_dump($e);
        }
    }

 }