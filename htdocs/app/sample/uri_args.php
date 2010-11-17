<?php
class Sample_UriArgs_c extends Yapafi_Controller {
    protected $has_args = 1;
    function run() {
        if ( !isset($this->args[0]) || !preg_match('!^[a-zA-Z0-9]{1,10}$!', $this->args[0] ) ){
            return404();
        }
        $this->stash = array(
            'title' => 'uriargs',
            'args'  => $this->args
        );
    }
}

