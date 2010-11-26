<?php
class Sample_RedirectOut_c extends Yapafi_Controller {
    function run() {
        // ちなみに、redirect()が吐くレスポンスコードは303がデフォルトです。第二引数にレスポンスコードを指定可能です。
        redirect('http://www.google.com/');
    }
}

