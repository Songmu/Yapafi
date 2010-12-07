<?php
class MIME_Base64_URLSafe{
    // replace (+ /) into (- _) and delete =
    
    static function encode($str){
        $str = base64_encode($str);
        return self::encodeBase64toURLSafe($str);
    }
    
    static function decode($str){
        $str = self::restructBase64fromURLSafe($str);
        return base64_decode($str);
    }
    
    static function encodeBase64toURLSafe($normal_base64){
        $str = str_replace('+', '-', $normal_base64);
        $str = str_replace('/', '_', $str);
        $str = str_replace('=', '', $str);
        return $str;
    }
    
    static function restructBase64fromURLSafe($url_safe_base64){
        $str  = preg_replace('![\t-\x0d ]!', '', $url_safe_base64);
        $str  = str_replace('-', '+', $str);
        $str  = str_replace('_', '/', $str);
        $mod4 = strlen($str) % 4;
        if ( $mod4 ){
            $str .= substr('====', $mod4);
        }
        return $str;
    }
    
}

