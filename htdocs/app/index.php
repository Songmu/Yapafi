<?php
class Index_c extends Yapafi_Controller {
    function run() {
        $this->setView(
            'index.tpl', 
            array(
                'title' => 'トップページ',
            )
        );
    }
}
?>
