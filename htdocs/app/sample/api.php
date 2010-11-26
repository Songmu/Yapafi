<?php
class Sample_Api_c extends Yapafi_Controller {
    protected $allow_exts = array('json'); //拡張子を"json"に指定します。
    function runGet() {
        $data = array(
            'title'   => 'JSONを返します',
            'content' => 'コントローラから値を返すとそれがレスポンスになります。API用途なんかに良いでしょう。',
            'data'    => array(2,3,5,7,9,11,13,17),
        );
        //データ構造をjsonとしてダンプします。
        return json_encode( $data ); // run*メソッドが値を返した場合、それがResponseBodyとして使われます。
    }
    function setHeader(){ //setHeaderメソッドをオーバーライドして正しく値をセットします。
        header('Content-Type: application/json; charset=utf-8 ;Content-Language: ja');
        set_no_cache();
    }
}

