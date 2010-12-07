<?php
class MIME_Base64_URLSafe{
    // replace (+ /) into (- _) and delete =
    
    static function encode($str){
        $str = base64_encode($str);
        $str = str_replace('+', '-', $str);
        $str = str_replace('/', '_', $str);
        $str = str_replace('=', '', $str);
        return $str;
    }
    
    static function decode($str){
        $str  = preg_replace('![\t-\x0d ]!', '', $str);
        $str  = str_replace('-', '+', $str);
        $str  = str_replace('_', '/', $str);
        $mod4 = strlen($str) % 4;
        if ( $mod4 ){
            $str .= substr('====', $mod4);
        }
        return base64_decode($str);
    }
    
}

