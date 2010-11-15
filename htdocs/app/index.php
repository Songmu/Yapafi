<?php
class Index_c extends Yapafi_Controller {
    function run() {
        $this->setView(
            'index.tpl', 
            array(
                'title' => 'トップページ',
            )
        );
        //$this->$i; //Fatal error: Cannot access empty propertyでerror_handlerに入るが。catch出来ない…。
    }
}

