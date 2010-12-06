<?php
require_once 'DataValidator.php';
class FormValidator {
    private $query;
    private $errors = array();
    private $error_messages = array();
    private $constraint_obj;
    
    function __construct($query){
        $this->query = $query;
        $this->data_validator = new DataValidator();
    }
    
    /* call like this.
    $v->check(array(
        'mail'       => array('REQUIRED', 'EMAIL_LOOSE');
        'mail mail2' => array('EQUALS'),
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
            $key_has_blank = !(strpos($key, ' ') === false);
            
            if( !$key_has_blank && !isset($this->query[$key]) ){
                $this->query[$key] = '';
            }
            
            // NOT REQUIREDでBLANKのときはチェックはスキップ
            if( $key_has_blank              || 
                $this->query[$key]          || 
                in_array($rules[0], array('NOT_NULL', 'REQUIRED', 'NOT_BLANK') ) )
            {
                foreach ( $rules as $rule ){
                    $keys = explode(' ', $key); // 空白で切る(DUPULICATEとか)
                    $values  = array();
                    $options = array();
                    foreach ( $keys as $v ){
                        $values[] = isset($this->query[$v]) ? $this->query[$v] : '' ;
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
                    
                    if ( !$this->data_validator->isValid( $rule_name, $values, $options ) ){
                        $this->errors[$key][] = $rule_name;
                    }
                }
            }
        }
    }
    
    /**
     * ルールを追加します。可変長引数でクラス名を指定します。
     */
    function loadConstraint(){
        $args = func_get_args();
        call_user_func_array(array($this->data_validator, 'loadConstraint'), $args );
    }
    function loadAllConstraint(){
        $this->data_validator->loadAllConstraint();
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
    
    function getErrors(){
        return $this->errors;
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
        $this->error_messages = array_merge( $this->error_messages, $msg_hash );
    }
    
    // $keyに対する"先頭の"エラーメッセージを返す。
    function getErrorMessage($key){
        if ( $this->isError( $key ) ){
            $constraint = $this->errors[$key][0];
            if ( isset($this->error_messages[$key.'.'.$constraint]) ){
                return $this->error_messages[$key.'.'.$constraint];
            }
            else{ //Constraintのデフォルトエラーメッセージを呼び出す
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
                else{ //Constraintのデフォルトエラーメッセージを呼び出す
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
        return $this->data_validator->getErrorMessage($constraint);
    }
    
    function setDefaultErrorMessages($arr){
        $this->data_validator->setMessages($arr);
    }
    
}

class FormValidatorException extends Exception{}

