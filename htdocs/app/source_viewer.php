<?php
class SourceViewer_c extends Yapafi_Controller {
    protected $has_args = 3;
    
    function runGet() {
        if ( count($this->args) == 0 ){
            return404();
        }
        else{
            $php_file = 'app/'.join('/',$this->args);
            
            if(  !file_exists( $php_file.'.php' ) ){
                $found = false;
                while ( preg_match('!^(.+)/[^/]+$!', $cntl_name, $matches ) ){
                    $php_file = $matches[1];
                    if ( file_exists( $php_file.'.php' ) ){
                        $found = true; break;
                    }
                }
                if ( !$found ){
                    return404();
                }
            }
            return h(file_get_contents($php_file.'.php'));
        }
    }
    
}

