<?php
require_once 'Mail/Address/MobileJp.php';
mb_internal_encoding('UTF-8');
mb_language('ja');
class Mail_Send_Japanese {
    //とりあえずベタに
    function send( $send_to, $tpl, array $args, array $additional_headers = array()){
        extract($args);
        $mail = str_replace("\r\n", "\n", file_get_contents( $tpl ) );
        eval("\$mail = \"$mail\";");
        list($mail_header, $body) = explode( "\n\n", $mail, 2 );
        
        $body = str_replace("\n", "\r\n", $body);
        
        $mail_headers = split("\n", $mail_header );
        $mail_header = '';
        $subject = '';
        foreach ( $mail_headers as $item ){
            if ( strpos($item, ': ') === false ){ continue; } //例外投げた方が良さげか？
            if ( stripos($item, 'subject: ') === 0 ){
                list( , $subject) = explode(':', $item, 2);
                $subject = trim($subject);
            }
            else{
                $mail_header .= $item . "\r\n";
            }
        }
        $mail_header .= "Mime-Version: 1.0\n";
        
        // docomo,au: shift_jis, softbank: utf8, other: jis
        $php_encoding = 'JIS';
        $char_set = 'iso-2022-jp';
        $mail_c = Mail_Address_MobileJp::factory();
        if( $mail_c->is_mobile_jp($send_to) ){
            if ( $mail_c->is_softbank($send_to) ){
                $php_encoding = 'UTF-8';
                $char_set = 'UTF-8';
            }
            elseif ( $mail_c->is_ezweb($send_to) ){
                $php_encoding = 'SJIS-win';
                $char_set = 'Shift_JIS';
            }
            elseif ( $mail_c->is_imode($send_to) ){
                $php_encoding = 'SJIS-win';
                $char_set = 'Shift_JIS';
            }
        }
        $mail_header .= "Content-Type :text/plain; charset=$char_set'";

        $subject = mb_encode_mimeheader($subject, $php_encoding);
        $body    = mb_convert_encoding($body, $php_encoding, 'UTF-8');

        return mail($send_to, $subject, $body, $mail_header);
    }
    
}
