<?php
class DataValidator {
    private $constraints = array();
    private $error_messages = array();
    
    function __construct($query){
        $this->query = $query;
        $this->loadConstraint('DataValidator_Default');
    }
    
    private function isValid( $rule, $values, $options = array() ){
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
        throw new DataValidatorException("Rule $rule is not exists!");
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

    /**
     * set like this.
     * $this->setErrorMessages(
     *      'NOT_NULL'  => 'Please set Value!',
     *      'BETWEEN'   => '[_1]は[_2]以上[_3]以下で入力してください',
     * );
     */
     
    function setErrorMessages($msg_hash){
        array_merge( $this->error_messages, $msg_hash );
    }

    function getErrorMessage($constraint){
        if ( isset($this->error_messages[$constraint]) ){
            return new DataValidator_ErrorMessage($this->error_messages[$constraint]);
        }
        else{ //Constraintのデフォルトエラーメッセージを呼び出す
            return new DataValidator_ErrorMessag($this->_getDefaultErrorMessage($constraint));
        }
    }

    function _getDefaultErrorMessage($constraint){
        foreach ( $this->constraints as $const_obj ){
            if ( $error_message = $const_obj->getErrorMessage($constraint) ){
                return $error_message;
            }
        }
        throw new DataValidatorException("Default Error Message of $constraint Is Not Exists!");
    }
    
}

final class DataValidator_ErrorMessage{
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



