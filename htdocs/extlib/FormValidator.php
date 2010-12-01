<?php
class FormValidator {
    private $constraints = array();
    private $query;
    private $errors = array();
    private $error_messages = array();
    
    function __construct($query){
        $this->query = $query;
        $constraints[] = new FormValidator_Constraint();
    }
    
    /* call like this.
    $v->check(array(
        'mail mail2' => array('DUPLICATION'),
        'name'       => array(
            REQUIRED,
            array('LENGTH', 0, 10),
            array('BETWEEN', 5, 2000),
        ),
        'age'       => array(
            array('BETWEEN', 20, 200),
        ),
        'hoge'      => array(
            array('REGEX', '/^\d{3}/'),
        ),
    ));
    
    */
    function check($validation_rules){
        foreach ( $validation_rules as $key => $rules ){
            // NOT REQUIREDで空白のときはチェックはスキップ
            if( $this->query[$key] !== '' || !in_array($rules[0], array('NOT_NULL', 'REQUIRED', 'NOT_BLANK')) ){
                foreach ( $rules as $rule ){
                    $keys = explode(' ', $key); // 空白区切りで切る(DUPULICATEとか)
                    $values  = array();
                    $options = array();
                    foreach ( $keys as $v ){
                        $values[] = $this->query[$key];
                    }
                    if ( count($values) === 1 ){
                        $values = $values[0]; //$valuesが一個しかない場合は、配列じゃなくする
                    }
                    if ( is_array($rule) ){
                        $rule_name = array_shift($rule);
                        $options = $rule;
                    }
                    else{
                        $rule_name = $rule;
                    }
                    
                    if ( !$this->_is_valid( $rule_name, $values, $options ) ){
                        $this->errors[$key][] = $rule;
                    }
                }
            }
        }
    }
    
    /**
     * ルールを追加します。可変長引数でクラス名を指定します。
     */
    function loadConstraint(){
        foreach ( func_get_args() as $class ){
            if ( !class_exists($class) ){
                $file_name = str_replace('_', '/', $class);
                require_once $file_name;
            }
            $constraints[] = new $class();
        }
    }
    
    private function _is_valid( $rule, $values, $options = array() ){
        $reverse = false;
        // ruleを"!"付きで呼び出したときは評価反転 '!NUMBER'とか
        if ( strpos($rule, '!') === 0 ){
            $rule = substr($rule, 1);
            $reverse = true;
        }
        $method = 'check'.$rule; 

        foreach ( $this->constraints as $constraint ){
            if ( method_exists( $constraint, $method ) ){
                if ( $options ){
                    $bool = $constraint->$method($values, $options);
                }
                else{
                    $bool = $constraint->$method($values);
                }
                return $reverse ? !$bool : $bool;
            }
        }
        throw new FormValidatorException("Rule $rule is not exists!");
    }
    
    function hasError(){
        return !empty($this->errors);
    }
    
    function isValid(){
        return !$this->hasError();
    }
    
    function isError($key){
        return isset($this->errors[$key]);
    }

    /* set like this
    $v->setErrorMessage(
        array(
            // "$name.$rule"
            'zip.JZIP' => 'Please input correct zip number.',
            'mails.DUPULICATION' => 'ddd',
        )
    );*/
    function setErrorMessages($msg_hash){
        array_merge( $this->error_messages, $msg_hash );
    }
    
    // $keyに対する"先頭の"エラーメッセージを返す。
    function getErrorMessage($key){
        if ( $this->isError( $key ) ){
            $constraint = $this->errors[$key][0];
            if ( isset($this->error_messages[$key.'.'.$constraint]) ){
                return $this->error_messages[$key.'.'.$constraint];
            }
            else{ //デフォルトエラーメッセージを呼び出す
                return $this->_getDefaultErrorMessage($constraint);
            }
        }
        return ''; //エラーがないときは空文字列を返す？
    }
    
    function getErrorMessages(){
        $result = array();
        foreach ( $this->errors as $key ){
            $result[$key] = $this->getErrorMessage($key);
        }
        return $result;
    }
    
    // $keyに対する"全ての"エラーメッセージを返す。
    function getAllErrorMessage($key){
        $result = array();
        if ( $this->isError( $key ) ){
            foreach ( $this->errors[$key] as $constraint ){
                if ( isset($this->error_messages[$key.'.'.$constraint]) ){
                    $result[] = $this->error_messages[$key.'.'.$constraint];
                }
                else{ //デフォルトエラーメッセージを呼び出す
                    $result[] = $this->_getDefaultErrorMessage($constraint);
                }
            }
        }
        return $result; //エラーがないときは空の配列を返す？
    }
    
    function getAllErrorMessages(){
        $result = array();
        foreach ( $this->errors as $key ){
            $result[$key] = $this->getAllErrorMessage($key);
        }
        return $result;
    }
    
    
    function _getDefaultErrorMessage($constraint){
        foreach ( $this->constraints as $const_obj ){
            if ( $error_message = $const_obj->getErrorMessage($constraint) ){
                return $error_message;
            }
        }
        throw new FormValidatorException("Default Error Message of $constraint Is Not Exists!");
    }
    
}

class FormValidatorException extends Exception{}

class FormValidator_Constraint extends FormValidator_AbstructConstraint {
    protected $error_messages = array(
        'REQUIRED'   => '[_1]を入力してください。',
        'NOT_NULL'   => '[_1]を入力してください。',
        'NOT_BLANK'  => '[_1]を入力してください。',
    );
    
    function checkREQUIRED($val){
        return $val !== '';
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
    
    function checkCOICE($val, $options){
        
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

abstract class FormValidator_AbstructConstraint {
    protected $error_messages = array();
    
    function setErrorMessages($msg_hash){
        $this->error_messages = array_merge( $this->error_messages, $msg_hash);
    }
    
    function getErrorMessage($rule){
        return 
            isset( $this->error_messages[$rule] ) ?
            new FormValidator_ErrorMessage($this->error_messages[$rule]) :
            false ;
    }
    
    function validate($rule, $val){
        $method = 'check'.$rule;
        return $this->$method($val);
    }
    
}

final class FormValidator_ErrorMessage{
    private $msg;
    
    function __construct($msg){
        $this->msg = $msg;
    }
    
    function assign(){
        $args = func_get_args();
        $len = count($args);
        for ( $i=1; $i<=$len; $i++ ){
            $this->msg = str_replace( "[_$i]", $args[$i], $this->msg );
        }
        return $this;
    }
    
    function __toString(){
        return $this->msg;
    }
}
