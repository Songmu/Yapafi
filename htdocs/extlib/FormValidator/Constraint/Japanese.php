<?php

class FormValidator_Constraint_Japanese extends FormValidator_AbstructConstraint {
    
    function checkHIRAGANA($val){
        return preg_match( '/\\A[ぁ-ゖー　\\s]*\\z/u', $val);
    }
    
    function checkKATAKANA($val){
        return preg_match( '/\\A[ァ-ヺー　\\s]*\\z/u', $val);
    }
    
    function checkJTEL($val){
        return preg_match('/\A0\d+-?\d+-?\d+\z/', $val);
    }
    
    function checkJZIP($val){
        if ( is_array( $val ) ){
            $val = $val[0] . '-' . $val[1];
        }
        return preg_match('/\A\d{3}-\d{4}\z/', $val);
    }
    
    
    function checkZENKAKU($val){
        return !preg_match('[\x00-\x7Eｦ-ﾟ]/', $val);
    }
    
    
    function checkHANKAKU_KATAKANA($val){
        return preg_match('/\A[ｦ-ﾟ ]\Z/', $val);
    }
    
    
    function checkJISX0208($val){
        // JIS X 0201のラテン文字も含んでしまうが、まあOKかなぁ？
        return $val === mb_convert_encoding( 
            mb_convert_encoding($val, 'ISO-2022-JP', 'UTF-8'),
            'UTF-8',
            'ISO-2022-JP'
        );
    }
    
    function checkCP932COMPATIBLE($val){
        return $val === mb_convert_encoding( 
            mb_convert_encoding($val, 'SJIS-win', 'UTF-8'),
            'UTF-8',
            'SJIS-win'
        );
    }
    
    
}
