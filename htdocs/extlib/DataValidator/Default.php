<?php
require_once 'FormValidator/Base.php';

class FormValidator_Default extends DataValidator_Base {
    protected $error_messages = array(
        'REQUIRED'   => '[_1]‚ð“ü—Í‚µ‚Ä‚­‚¾‚³‚¢B',
        'NOT_NULL'   => '[_1]‚ð“ü—Í‚µ‚Ä‚­‚¾‚³‚¢B',
        'NOT_BLANK'  => '[_1]‚ð“ü—Í‚µ‚Ä‚­‚¾‚³‚¢B',
    );
    
    function checkREQUIRED($val){
        if ( is_array($val) ){
            return !empty($val);
        }
        else{
            return $val !== '';
        }
    }
    function checkNOT_NULL($val){
        return $this->checkREQUIRED($val);
    }
    function checkNOT_BLANK($val){
        return $this->checkREQUIRED($val);
    }
    
    function checkNUMBER($val){
        return (bool)preg_match('/\A[-+]?(?:(?:[1-9][0-9]*)(?:\.[0-9]+)?|0\.[0-9]+)\z/', $val);
    }
    
    function checkALNUM($val){
        return (bool)preg_match('/\A[0-9a-zA-Z]+\z/', $val);
    }
    
    function checkINT($val){
        return (bool)preg_match('/\A[-+]?[1-9][0-9]*\z/', $val);
    }
    
    function checkNUM_STRING($val){
        return (bool)preg_match('/\A[0-9]+\z/', $val);
    }
    
    function checkASCII($val){
        return (bool)preg_match('/\A[\x21-\x7E]+\z/', $val);
    }
    
    function checkCHOICE($val, $options){
        if ( is_array($options[0]) ){
            $options = $options[0];
        }
        foreach ( $options as $choice ){
            if ( $choice === $val ){
                return true;
            }
        }
        return false;
    }
    function checkIN($val, $options){
        $this->checkCHOICE($val, $options);
    }
    
    function checkDUPLICATION($values){
        return $values[0] === $values[1];
    }
    
    function checkBETWEEN($val, $range){
        return is_numeric($val) && $val>=$range[0] && $val<=$range[1];
    }
    
    function checkLENGTH($val, $options){
        $len = strlen($val);
        return $len<=$options[0] && $len>=$options[1];
    }
    
    function checkMB_LENGTH($val, $options){
        $enc = isset($options[2]) ? $options[2] : 'UTF-8';
        $len = mb_strlen($val, $enc);
        return $len<=$options[0] && $len>=$options[1];
    }
    
    function checkREGEX($val, $options){
        foreach ( $options as $regex ){
            if ( !preg_match($regex, $val) ){
                return false;
            }
        }
        return true;
    }
    
    
    function checkREGEX_ANY($val, $options){
        foreach ( $options as $regex ){
            if ( preg_match($regex, $val) ){
                return true;
            }
        }
        return false;
    }
    
    
}
