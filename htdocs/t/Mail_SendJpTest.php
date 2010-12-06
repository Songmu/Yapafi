<?php
set_include_path(get_include_path().PATH_SEPARATOR.'../extlib/');

require_once 'Mail/SendJp.php';

$mailer = new Mail_SendJp();

if ( $mailer->send(
    'test@example.com, test2@mail.example.com',
    'testtpl.txt',
    array(
        'subject' => '日本語サブジェクト',
        'var'     => '本文への差込文字列です。',
    )
) ){
    echo 'OK';
}
else{
    echo 'NG';
}

