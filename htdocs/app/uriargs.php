<?php
class Uriargs_c extends Yapafi_Controller {
    protected $has_args = 1;
    function run() {
        $this->setView(
            'uriargs.tpl',
            array(
                'title' => 'uriargs',
                'args'  => $this->args,
            )
        );
    }
}
?>