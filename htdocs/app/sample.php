<?php
class Sample_c extends Yapafi_Controller {
    function run() {
        $this->setView(
            'sample.tmpl',
            array(
                'title' => 'sample',
            )
        );
    }
}
?>