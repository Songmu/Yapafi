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
    
    function check($validation_rules){
        foreach ( $validation_rules as $key => $rules ){
            // NOT REQUIREDで空白のときは残りのチェックはスキップとかそういうロジックが必要だねぇ。
            foreach ( $rules as $rule ){ // DUPULICATION等は保留
                if ( !$this->_is_valid( $rule, $this->query[$key] ) ){
                    $this->errors[$key][] = $rule;
                }
            }
        }
    }
    
    /**
     * ルールを追加します。可変長引数でクラス名を指定します。
     */
    function loadConstraint(){
        foreach ( func_get_args() as $class ){
            $constraints[] = new $class();
        }
    }
    
    private function _is_valid( $rule, $value, $options = array() ){
        $method = 'is'.$rule;
        foreach ( $this->constraints as $constraints ){
            if ( method_exists( $constraints, $method ) ){
                return $constraints->$method($value);
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
    
    // $keyに対する"先頭の"エラーメッセージを返す。差込とかにも対応したいが。
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
        $method = 'getErrorMessageOf'.$constraint;
        foreach ( $this->constraints as $const_obj ){
            try {
                $error_message = $const_obj->$method();
                return $error_message;
            }
            catch ( Exception $ex ){
                continue;
            }
        }
        throw new FromValidatorException("Default Error Message of $constraint Is Not Exists!");
    }
    
}

class FormValidatorException extends Exception{}

class FromValidator_Constraint extends FormValidator_ConstraintAbstruct{
    protected $error_messages = array(
        'TEL'   => '電話番号を正しく入力してください'
    );
    
    function isREQUIRED($val){
        return $val !== '';
    }
    
    function isNOT_NULL($val){
        return $this->isREQUIRED($val);
    }
    
    function isNUMBER($val){
        return preg_match('/^\d*$/', $val);
    }
    
    
    function isDUPURICATION($val1, $val2){ // 変....
        
    }
    
    function isBETWEEN($val, $from, $to){ // 変....
        
    }
    
    
    function getErrorMessageOfNUMBER(){
        return '数値で入力してください';
    }
    
    
    
}

abstract class FormValidator_ConstraintAbstruct {
    protected $error_messages = array();
    
    function __call($name, $arguments){
        // getErrorMessageOf*** が定義されていなかったときに $error_argsから取得。
        if ( preg_match('/^getErrorMessageOf(.*)$/', $name, $matches) && isset( $this->error_messages[$matches[1]] ) ) {
            return $this->error_messages[$matches[1]];
        }
        if ( preg_match('/^is(.*)$/', $name, $matches ) && method_exists( $this, $matches[1] ) ){
            $method = $matches[1];
            return $this->$method();//うーん。
        }
        throw new Exception("Method $name not exists!");
    }
    
    function validate($rule, $val){
        return $this->$rule($val);
    }
    
}
