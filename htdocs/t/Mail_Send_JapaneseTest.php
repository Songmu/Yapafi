<?php
set_include_path(get_include_path().PATH_SEPARATOR.'../extlib/');

require_once 'Mail/Send/Japanese.php';

$mailer = new Mail_Send_Japanese();

if ( $mailer->send(
    'hogehogegeogege13934kf9v8a@docomo.ne.jp',
    'hoge.txt',
    array(
        'subject' => 'あれれ？',
        'var'     => 'おかしいぞ？',
    )
) ){
    echo 'OK';
}
else{
    echo 'NG';
}

