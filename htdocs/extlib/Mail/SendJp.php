<?php
require_once 'Mail/Address/MobileJp.php';
mb_internal_encoding('UTF-8');
mb_language('ja');

class Mail_SendJp {
    public $default_send_encoding = 'JIS'; // ISO-2022-JP-MS の方が良い？
    public $tpl_class = 'Mail_SendJp_TemplatePHPString';
    
    
    function send(  $send_to, $tpl, array $args, array $additional_headers = array() ){
        $tpl_builder = new $this->tpl_class;
        list($mail_header, $body) = $tpl_builder->render($tpl, $args);
        list($subject, $mail_header) = self::_parse_header($mail_header);
        
        return self::_send_simple($send_to, $subject, $body, $mail_header, self::$default_send_encoding );
    }
    
    function send_intelligence( $send_to, $tpl, array $args, array $additional_headers = array()){
        $tpl_builder = new $this->tpl_class;
        list($mail_header, $body) = $tpl_builder->render($tpl, $args);
        list($subject, $mail_header) = self::_parse_header($mail_header);
        
        $mails = preg_split('!\s*,\s*!', $send_to);
        $groups_each_encoding = array();
        foreach ( $mails as $mail_addr ){
            $php_encoding = $this->getEncodingFromMail($mail_addr);
            if ( !isset( $groups_each_encoding[$php_encoding] ) ){
                $groups_each_encoding[$php_encoding] = array();
            }
            $groups_each_encoding[$php_encoding][] = $mail_addr;
        }
        foreach ( array_keys( $groups_each_encoding ) as $encoding ){
            $mail_to = join( ', ',$groups_each_encoding[$encoding] );
            $result = self::_send_simple($mail_to, $subject, $body, $mail_header, $encoding);
            if ( !$result ){ return false; }
        }
        return true;
    }
    
    static function _send_simple( $send_to, $subject, $body, $mail_header, $encoding ){
        $subject = mb_encode_mimeheader($subject, $encoding);
        $body    = mb_convert_encoding($body, $encoding, 'UTF-8');
        $char_set = self::_get_charset($encoding);
        $mail_header .= 
            "Content-Type :text/plain; charset=$char_set\r\n".
            "Content-Transfer-Encodng: ".self::_get_transfer_encoding($char_set)."\r\n";
        return mail($send_to, $subject, $body, $mail_header);
    }
    
    static function _parse_header( $mail_header ){
        $mail_headers = split("\n", $mail_header );
        $mail_header = '';
        $subject = '';
        foreach ( $mail_headers as $item ){
            if ( strpos($item, ': ') === false ){ continue; } //例外投げた方が良さげか？
            if ( stripos($item, 'subject: ') === 0 ){
                list( , $subject) = explode(': ', $item, 2);
            }
            else{
                $mail_header .= $item . "\r\n";
            }
        }
        $mail_header .= "Mime-Version: 1.0\r\n";
        return array( $subject, $mail_header );
    }
    
    static function _get_charset( $encoding ){
        $lookup = array(
            'ISO-2022-JP-MS' => 'ISO-2022-JP', // 半角カナ・機種依存文字を含むISO-2022-JPの上位互換
            'JIS'            => 'ISO-2022-JP', // 半角カナを含むISO-2022-JPの上位互換
            'EUCJP-WIN'      => 'EUC-JP',      // winとか言いつつ、実はeucJP-ms Linuxで使われているcp932互換のeuc-jp
            'CP51932'        => 'EUC-JP',      // Windows用で使われているEUC-JP
            'SJIS'           => 'Shift_JIS',
            'SJIS-WIN'       => 'Shift_JIS',   // 所謂cp932 Shift_JIS + Windows機種依存文字
            'UTF-8'          => 'UTF-8',
         );
        $encoding = strtoupper($encoding);
        return isset($lookup[$encoding]) ? $lookup[$encoding] : '';
    }
    
    static function _get_transfer_encoding( $char_set ){
        $lookup = array(
            'ISO-2022-JP' => '7bit', 
            'UTF-8'       => '8bit',
            'Shift_JIS'   => '8bit', 
            'EUC-JP'      => '8bit',
         );
        return $lookup[$char_set];
    }
    
    function getEncodingFromMail( $send_to ){
        // docomo,au: shift_jis, softbank: utf8, other: default
        $php_encoding = $this->default_send_encoding;
        $mail_c = Mail_Address_MobileJp::factory();
        if( $mail_c->is_mobile_jp($send_to) ){
            if ( $mail_c->is_softbank($send_to) ){
                $php_encoding = 'UTF-8';
            }
            elseif ( $mail_c->is_ezweb($send_to) ){
                $php_encoding = 'SJIS-win';
            }
            elseif ( $mail_c->is_imode($send_to) ){
                $php_encoding = 'SJIS-win';
            }
        }
        return $php_encoding;
    }
    
}


class Mail_SendJp_TemplatePHPString {
    function render( $tpl, array $args ){
        extract($args);
        $mail = str_replace("\r\n", "\n", file_get_contents( $tpl ) );
        eval("\$mail = \"$mail\";");
        return explode( "\n\n", $mail, 2 );
    }
}

class Mail_SendJp_TemplatePHPCode {
    function render( $tpl, array $args ){
        ob_start();
        ob_implicit_flush(0);
        extract($args);
        require $tpl;
        $mail = str_replace("\r\n", "\n", ob_get_clean());
        return explode( "\n\n", $mail, 2 );
    }
}


