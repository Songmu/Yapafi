<?php
class Sample_Sjis_c extends Yapafi_Controller {
    // コントローラ内で以下のように出力エンコード(PHP形式)を指定します。
    protected $output_encoding = 'SJIS-win';
    
    // 後の流れは同じです。
    // ただし、エンコード先文字コードが何であれ、
    // コントローラもテンプレートも*UTF8で記述する*ことに注意してください。
    function runGet() {
        $this->stash = array(
            'title' => '文字コードを変換して出力',
        );
    }
}

