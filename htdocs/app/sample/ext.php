<?php
class Sample_Ext_c extends Yapafi_Controller {
    protected $allow_exts = array('html',);
    function run() {
        $this->stash = array(
            'title' => 'Ext',
        );
    }
}

