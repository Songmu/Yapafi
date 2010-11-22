<?php
require_once('MDB2.php');

/* 
MDB2PLUS is wrapper class of MDB2.
Its interface is same as MDB2.
(Some method doesn't work(eg. MDB2::isResult). But, these are
 not too necessary.)

When occurring error, it not returns PEAR::Error object, 
but raise MDB2PLUSException.

You can handle errors only using try-catch syntax, need not
check each returning value of method is PEAR::Error or not.

There is one more feature. Fetch mode is 'ASSOC' as default.
It is only my own favor, but I think most of people like it 
too.

Author: Masayuki Matsuki
*/


class MDB2PLUS {
    /* __callStatic is new feature of PHP5.3!
    function __callStatic( $name, $args ){
        return MDB2PLUS::setFetchModeAssoc ( 
            new MDB2PLUS_Proxy(
                call_user_func_array("MDB2::$name", $args)
            )
        );
    } */
    
    function singleton($dsn, $options = false) {
        return MDB2PLUS::_setFetchModeAssoc(
            new MDB2PLUS_Proxy( MDB2::singleton( $dsn, $options) )
        );
    }
    function factory($dsn, $options = false) {
        return MDB2PLUS::_setFetchModeAssoc(
            new MDB2PLUS_Proxy( MDB2::factory( $dsn, $options) )
        );
    }
    function connect($dsn, $options = false) {
        return MDB2PLUS::_setFetchModeAssoc(
            new MDB2PLUS_Proxy( MDB2::connect( $dsn, $options) )
        );
    }
    
    static function _setFetchModeAssoc( $dbcon ){
        $dbcon->setFetchMode(MDB2_FETCHMODE_ASSOC);
        return $dbcon;
    }
    
}


class MDB2PLUS_Proxy {
    private $obj;
    
    function __construct($obj) {
        if ( PEAR::isError($obj) ){
            throw new MDB2PLUSException($obj->getMessage());
        }
        $this->obj = $obj;
    }
    
    function __call($name, $args) {
        if ( method_exists($this->obj, $name) ){
            $tmp = call_user_func_array(array($this->obj, $name), $args);
            if ( is_object( $tmp ) ){
                return new MDB2PLUS_Proxy( $tmp );
            }
            else{
                return $tmp;
            }
        }
        else {
            throw new MDB2PLUSException('method "'. $name .'" not found');
        }
        
    }
}

class MDB2PLUSException extends Exception {}

?>