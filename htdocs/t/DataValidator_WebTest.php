<?php
set_include_path(get_include_path().PATH_SEPARATOR.'../extlib');
require_once 'DataValidator/Base.php'; //
require_once 'DataValidator/Web.php';


class DataValidator_WebTest extends PHPUnit_Framework_TestCase{
    
    public function testURL(){
        $v = new DataValidator_Web();
        
        $ok = array(
            'http://www.example.com',
            'http://www.example.com/',
            'http://www.example.com:8088',
            'http://www.example.com:8088/',
            'https://www.example.com:8088/?hoge=fuga',
            'https://www.example.com/?hoge=fuga#fragment',
            'https://www.example.com/hoge',
            'https://www.example.com/hoge.html',
            'https://www.example.com/hoge.html;ddd/fuga,ee/%43%22',
         );
        
        foreach ( $ok as $val ){
            $this->assertTrue($v->checkURL($val));
        }
        
        $ng = array(
            'ftp://www.example.com',
            'shttp://www.example.com/',
            'https://www.example.com:8088/hoge.html;;ddd/',
            'http:///hoge.fuga/',
        );
        
        foreach ( $ng as $val ){
            $this->assertFalse($v->checkURL($val));
        }
    }





}
