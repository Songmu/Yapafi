<?php
require_once 'Mail/SendJp.php'; // Mail_SendJp モジュールを使います。
class Sample_SendMail_c extends Yapafi_Controller {

    function runGet() {
        $mailer = new Mail_SendJp(); //オブジェクトを作成します。
        /* コメントを解除するとメールを本当に送ってしまいます。
         * Mail_SendJpでは内部的にはmail()関数を使っています。
        $success = $mailer->send(
            'hoge@example.com, fuga@example.com', // 宛先(カンマ区切りで複数指定可能
            'mailtpl.txt', // メールテンプレート
            array(         // テンプレートへの差込文字列を連想配列で指定
                'subject' => '日本語サブジェクト',
                'var'     => '本文への差込文字列です。',
            )
        );*/ 
        
    }
}



