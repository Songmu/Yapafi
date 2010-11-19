<?php
require_once 'extlib/markdown.php';
class Column_c extends Yapafi_Controller {
    protected $has_args = 1;
    protected $allow_exts = array('html',);
    
    static $data_dir = 'work/mkdntxts/';
    
    function runGet() {
        if ( empty( $this->args ) ){
            $this->setView('column_list.tpl', array(
                'title'       => 'コラム一覧',
                'column_list' => $this->getColumnList(),
            ));
        }
        else{
            $mkdn_file = self::$data_dir . $this->args[0] . '.mkdn';
            if ( !file_exists( $mkdn_file )){
                return404();
            }
            $this->stash = array(
                'title' => $this->args[0],
                'main_contents' => raw_str(Markdown(file_get_contents($mkdn_file))),
            );
        }
    }
    
    // 本当はモデルに書くべき処理だけど…。
    function getColumnList(){
        $column_list = array();
        foreach ( glob(self::$data_dir.'*.mkdn') as $filename ){
            $fh = fopen($filename, 'r');
            $title = fgets($fh);
            if ( !$title ) { fclose($fh); continue; }
            
            preg_match( '![^/]+(?=\.mkdn$)!', $filename, $matches );
            $filename = $matches[0];
            
            $column_list[] = array(
                'title' => trim( $title, '#'),
                'link'  => $filename,
            );
            fclose($fh);
        }
        return $column_list;
    }
}

