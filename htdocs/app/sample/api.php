<?php
class Sample_Api_c extends Yapafi_Controller {
    protected $allow_exts = array('json');
    function run() {
        $data = array(
            'title'   => 'JSONを返します',
            'content' => 'コントローラから値を返すとそれがレスポンスになります。API用途なんかに良いでしょう。',
            'data'    => array(2,3,5,7,9,11,13,17),
        );
        return json_encode( $data );
    }
    function setHeader(){
        header('Content-Type: application/json; charset=utf-8 ;Content-Language: ja');
        set_no_cache();
    }
}

