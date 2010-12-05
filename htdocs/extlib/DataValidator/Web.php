<?php
class DataValidator_Web extends DataValidator_Base {
    
    function checkURL($val){
        
        
    }
    
    function checkEMAIL($val){
        return (bool)preg_match('@\A(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&\'*+/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&\'*+/=?\^`{}~|\-]+))*)|(?:"(?:\\[^\r\n]|[^\\"])*")))\@(?:(?:(?:[a-zA-Z0-9_!#\$\%&\'*+/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&\'*+/=?\^`{}~|\-]+))*))\z@', $val);
    }
    
    function checkEMAIL_LOOSE($val){
        
    }
    
}

