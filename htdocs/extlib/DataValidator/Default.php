<?php
require_once 'DataValidator/Base.php';

class DataValidator_Default extends DataValidator_Base {
    protected $error_messages = array(
        'REQUIRED'      => '[_1]を入力してください。',
        'NOT_NULL'      => '[_1]を入力してください。',
        'NOT_BLANK'     => '[_1]を入力してください。',
        'ALNUM'         => '[_1]は半角英数字で入力して下さい',
        'ASCII'         => '[_1]は半角文字で入力して下さい',
        'NUMBER'        => '[_1]は数値で入力して下さい',
        'INT'           => '[_1]には整数を入力してください',
        'UINT'          => '[_1]には正の整数を入力してください',
        'NUM_STRING'    => '[_1]は数字を入力してください',
        'CHOICE'        => '[_1]の入力が不正です',
        'IN'            => '[_1]の入力が不正です',
        'EQUALS'        => '[_1]と[_2]には同じ値を入力してください',
        'BETWEEN'       => '[_1]には[_2]から[_3]の間の値を入力してください',
        'LENGTH'        => '[_1]には半角[_2]文字以上から[_3]文字以下で入力してください',
        'MB_LENGTH'     => '[_1]には[_2]文字以上から[_3]文字以下で入力してください',
        'REGEX'         => '[_1]の入力形式が正しくありません',
        'REGEX_ANY'     => '[_1]の入力形式が正しくありません',
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
    
    function checkUINT($val){
        return (bool)preg_match('/\A[1-9][0-9]*\z/', $val);
    }
    
    function checkNUM_STRING($val){
        return (bool)preg_match('/\A[0-9]+\z/', $val);
    }
    
    function checkSINGLE_LINE($val){
        return !preg_match('/[\r\n]/ms', $val);
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
    
    function checkEQUALS($values){
        return $values[0] === $values[1];
    }
    
    function checkBETWEEN($val, $range){
        return is_numeric($val) && $val>=$range[0] && $val<=$range[1];
    }
    
    function checkLENGTH($val, $options){
        $len = strlen($val);
        return $len>=$options[0] && $len<=$options[1];
    }
    
    function checkMB_LENGTH($val, $options){
        $enc = isset($options[2]) ? $options[2] : 'UTF-8';
        $len = mb_strlen($val, $enc);
        return $len>=$options[0] && $len<=$options[1];
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
