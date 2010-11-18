<?php
class Sample_Sjis_c extends Yapafi_Controller {
    protected $output_encoding = 'SJIS-win';
    function run() {
        $this->stash = array(
            'title' => '文字コードを変換して出力',
        );
    }
}

