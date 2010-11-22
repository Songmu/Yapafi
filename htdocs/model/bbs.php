<?php

class Model_Bbs{
    static $_datafile = 'work/bbs/data.tsv';
    
    static function update($name, $comment){
        $name = preg_replace('![\n\r\t\x00]!ms', '', $name);
        $comment = preg_replace('![\n\r\t\x00]!ms', '', $comment);
        
        file_put_contents(
            self::$_datafile,
            date('Y/m/d H:i:s', time()) ."\t".$name."\t".$comment."\n",
            FILE_APPEND | LOCK_EX
        );
    }
    
    static function getComments(){
        $comments = array();
        $comment_data = explode("\n", file_get_contents(self::$_datafile) );
        $comment_data = array_reverse( $comment_data );
        
        foreach ( $comment_data as $comment ){
            $tmp_arr =  explode("\t", $comment);
            if ( count($tmp_arr) < 3 ){ continue; }
            $comments[] = array(
                'time'     => $tmp_arr[0],
                'name'     => $tmp_arr[1],
                'comment'  => $tmp_arr[2],
            );
        }
        return $comments;
    }
    
}
