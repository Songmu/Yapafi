<?php
abstract class DataValidator_Base {
    protected $error_messages = array();
    
    function getErrorMessage($rule){
        return 
            isset( $this->error_messages[$rule] ) ?
            $this->error_messages[$rule] : false  ;
    }
    
}

