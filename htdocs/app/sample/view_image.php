<?php
class Sample_ViewImage_c extends Yapafi_Controller {
    const UPLOAD_DIR = 'work/uploaded/';
    protected $allow_exts = array('png','jpg','gif');
    protected $has_args = 1;
    
    static $mime_type = array(
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'gif'   => 'image/gif',
    );
    
    function runGet() {
        $image_file_name = self::UPLOAD_DIR . $this->args[0] . '.' . $this->ext;
        if ( count($this->args) !== $this->has_args || !file_exists($image_file_name) ){
            return404();
        }
        return file_get_contents($image_file_name);
    }
    
    function setHeader(){
        header('Content-Type: ' . self::$mime_type[$this->ext]);
    }
    
    
}

