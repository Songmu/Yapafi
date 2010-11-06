<?php
class Index_c extends Yapafi_Controller {
    function run() {
        $this->setView(
            'index.tmpl', 
            array(
                'title' => 'トップページ',
            )
        );
    }
}
?>