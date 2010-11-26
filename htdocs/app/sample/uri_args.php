<?php
class Sample_UriArgs_c extends Yapafi_Controller {
    // URL引数は、"controller_name/args1/args2/..."と言うイメージです。
    // args1..が $this->argsに配列で格納されます。
    // $has_argsメンバ変数に取りうる引数の数(最大値)を指定します。
    protected $has_args = 1;
    
    // URL引数のとりうる値は以下： 英字半角小文字、半角数字、以下の半角記号 + - , ; % _
    function runGet() {
        if ( !isset($this->args[0]) || !preg_match('!^[a-zA-Z0-9]{1,10}$!', $this->args[0] ) ){
            return404(); // 引数が無い場合は 404 NOT FOUNDを返します。( return404()は404を返す関数 )
        }
        $this->stash = array(
            'title' => 'uriargs',
            'args'  => $this->args //URL引数をテンプレートに渡しています。
        );
    }
}

