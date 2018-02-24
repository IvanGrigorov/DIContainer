<?php 

namespace WorkflowErros;
use \Exception as Exception;

    final class ConvertingInterfaceToClassNameErro extends Exception {

        public function _construct($interface) {
            parent::_construct("Interface name is not correctly given: ".$interface);
        }
    }


