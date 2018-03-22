<?php
/*
 * File: DIContractConfigParser.php
 * Project: PHPDI
 * File Created: Thursday, 22nd March 2018 9:51:49 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Thursday, 22nd March 2018 11:48:44 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */



final class DIContractConfigParser {

    private $filePathToConfig;
    private $decodedConfig;

    public function __construct($filePathToConfig) {
        $this->filePathToConfig = $filePathToConfig;
        $this->decodeConfig();
    }

    private function getConfigContentFromFile() {
        $configContent = file_get_contents($this->filePathToConfig);
        return $configContent;
    }

    private function decodeConfig() {
        $this->decodedConfig = json_decode($this->getConfigContentFromFile(), true);
    }

    public function getMappedObject() {
        return $this->decodedConfig["mapInstances"];
    }

    public function getMapValueTypes() {
        return $this->decodedConfig["mapValueTypes"];
    }

    public function getMapParameterBasedObjects() {
        return $this->decodedConfig["mapParameterBasedObjects"];
    }
}