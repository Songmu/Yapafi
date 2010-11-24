<?php
set_include_path(get_include_path().PATH_SEPARATOR.'../extlib/');

require_once 'Mail/SendJp.php';

$mailer = new Mail_SendJp();

if ( $mailer->send(
//    'hogehogegeogege13934kf9v8a@docomo.ne.jp, hoge@ezweb.ne.jp, masayuki.matsuki@toppan.co.jp, y.songmu@gmail.com, m09060034225@tovodafone.ne.jp',
    'y.songmu@gmail.com, masayuki.matsuki@toppan.co.jp, m09060034225@t.vodafone.ne.jp',
    'hoge.txt',
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

