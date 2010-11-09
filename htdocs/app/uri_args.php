<?php
class UriArgs_c extends Yapafi_Controller {
    protected $has_args = 1;
    function run() {
        $this->setView(
            'uri_args.tpl',
            array(
                'title' => 'uriargs',
                'args'  => $this->args,
            )
        );
    }
}
?>
