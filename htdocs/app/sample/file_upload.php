<?php
class Sample_FileUpload_c extends Yapafi_Controller {
    const UPLOAD_DIR = 'work/uploaded/';
    
    function runGet() {
        $this->stash['images'] = array();
        $dh = opendir(self::UPLOAD_DIR);
        while ( $file = readdir($dh) ){
            if ( !preg_match('/\.(?:png|gif|jpg)$/', $file ) ){
                continue;
            }
            $this->stash['images'][] = $file;
        }
    }
    
    function runPost() {
        $file_name = strtolower($_FILES['imgfile']['name']);
        // 本当はこの程度のチェックじゃいけません。
        if ( preg_match( '/^[-_a-zA-Z0-9]+\.(?:jpg|png|gif)$/', $file_name ) ){
            move_uploaded_file($_FILES["imgfile"]["tmp_name"], self::UPLOAD_DIR . $file_name);
        }
        redirect('./file_upload');
    }
    
}

