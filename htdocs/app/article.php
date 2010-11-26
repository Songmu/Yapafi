<?php
require_once 'app/column.php';
class Article_c extends Column_c {
    protected $data_dir = 'work/articles/';
    
    function runGet(){
        parent::runGet();
        if ( empty( $this->args ) ){
            $this->stash['title'] = '記事一覧';
            $this->stash['dir']   = 'article';
        }
        else{
            $this->setView('column.tpl');
        }
        
    }
}

