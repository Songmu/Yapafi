<?php
require_once 'DataValidator.php';

class DataValidatorAll extends DataValidator {i
    function __construct($query){
        $inc_dirs = explode(PATH_SEPARATOR, get_include_path());
        $lib_dir;
        foreach ( $inc_dirs as $inc_dir ){
            if ( file_exists( $inc_dir . '/DataValidator') ){
                $lib_dir = $inc_dir; break;
            }
        }
        if ( !$lib_dir ){
            throw new Exception();
        }
        $handle = opendir($lib_dir);
        while (false !== ($file = readdir($handle))) {
            if ( preg_match('/^(?:.+)(?=\.php$)/', $file, $matches) ) {
                if ( $matches[0] !== 'base' ){
                    $this->loadConstraint('DataValidator_'.$matches[0]);
                }
            }
        }
        closedir($handle);
    }
    
    
}
