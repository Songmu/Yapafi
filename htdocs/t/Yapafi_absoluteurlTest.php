<?php
require_once '../yapafi.php';


class Yapafi_AbsoluteUrlTest extends PHPUnit_Framework_TestCase{
    
    public function testAbsUrl(){
        $this->assertEquals(
            'http://www.example.jp/html.php',
            get_absolute_url('http://www.example.jp/hoge/fuga/piyo.html', '../../html.php')
        );
        
        $this->assertEquals(
            'http://www.example.jp/ht/ml.php',
            get_absolute_url('http://www.example.jp/hoge/fuga/piyo.html', '../../ht/./ml.php')
        );
        
        $this->assertEquals(
            'http://www.example.jp/hoge/fuga/html.php',
            get_absolute_url('http://www.example.jp/hoge/fuga/piyo.html', './html.php')
        );
        
        $this->assertEquals(
            'http://www.example.jp/hoge/fuga/gete/html.php',
            get_absolute_url('http://www.example.jp/hoge/fuga/piyo.html', 'gete/html.php')
        );
        
        
        $this->assertEquals(
            'http://www.example.jp/hoge/fuga/html.php',
            get_absolute_url('http://www.example.jp/hoge/fuga/piyo.html', 'gete/../html.php')
        );

        $this->assertEquals(
            'http://www.example.jp/gete/html.php',
            get_absolute_url('http://www.example.jp/hoge/fuga/piyo.html', '/gete/html.php')
        );
        
        $this->assertEquals(
            'http://www2.example.jp/gete/html.php',
            get_absolute_url('http://www.example.jp/hoge/fuga/piyo.html', '//www2.example.jp/gete/html.php')
        );
        
        
    }


}
