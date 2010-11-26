<?php
class Sample_Ext_c extends Yapafi_Controller {
    // URLに拡張子をつけたい場合には $allow_exts に許容する拡張子を配列で指定します。
    protected $allow_exts = array('html',);
    
    function runGet() {
        // $this->ext に拡張子文字列が格納されています。
        // 拡張子に応じてファイル形式(json, yaml等)を出し分ける際の判定に使うと良いでしょう。
        $ext = $this->ext;
        
        $this->stash = array(
            'title' => $ext, 
        );
    }
}

