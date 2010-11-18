<?php
class Sample_AutoEscape_c extends Yapafi_Controller {
    function runGet() {
        $this->setView( '' ,array(
            'title' => 'Ext',
            'auto_escape' => '<pre>あいうえお</pre>',
            'no_escape' => raw_str('<pre>あいうえお</pre>'),
        ));
    }
}

