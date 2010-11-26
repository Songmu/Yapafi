<?php
class Sample_Redirect_c extends Yapafi_Controller {
    function run() {
        // redirect関数を準備しています。
        // 相対パスを指定しても、RFCに即し、絶対パスに置き換えてからクライアントにレスポンスを返します。
        redirect('../index');
    }
}

