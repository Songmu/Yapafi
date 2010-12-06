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
            'http:///hoge.fuga/',
        );
        
        foreach ( $ng as $val ){
            $this->assertFalse($v->checkURL($val));
        }
    }

    public function testEMAIL(){
        $v = new DataValidator_Web();
        
        $ok = array(
            'test@example.com',
            '"aaa@bbbb"@example.com',
            'bcc.ddd+eee@host.example.com',
        );
        
        foreach ( $ok as $val ){
            $this->assertTrue($v->checkEMAIL($val));
        }
        
        $ng = array(
            'testexample.com',
            'aaa@bbbb@example.com',
            'bcc.ddd+eee.@ezweb.ne.jp',
            'bcc.ddd..eee@ezweb.ne.jp',
        );
        
        foreach ( $ng as $val ){
            $this->assertFalse($v->checkEMAIL($val));
        }
    }

    public function testEMAIL_LOOSE(){
        $v = new DataValidator_Web();
        
        $ok = array(
            'test@example.com',
            '"aaa@bbbb"@example.com',
            'bcc.ddd+eee@host.example.com',
            'bcc.ddd+eee.@ezweb.ne.jp',
            'bcc.ddd..eee@ezweb.ne.jp',
         );
        
        foreach ( $ok as $val ){
            $this->assertTrue($v->checkEMAIL_LOOSE($val));
        }
        
        $ng = array(
            'testexample.com',
            'aaa@bbbb@example.com',
        );
        
        foreach ( $ng as $val ){
            $this->assertFalse($v->checkEMAIL_LOOSE($val));
        }
    }

}
