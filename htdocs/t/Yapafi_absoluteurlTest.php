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
    
    public function testD(){
        $this->assertEquals(
'array(2) {
  ["title"]=>
  string(6) "fruits"
  ["hash"]=>
  array(3) {
    ["apple"]=>
    string(5) "ringo"
    ["orange"]=>
    string(5) "mikan"
    ["banana"]=>
    string(6) "banana"
  }
}
',
            d(array(
                'title' => 'fruits',
                'hash'  => array(
                    'apple'  => 'ringo',
                    'orange' => 'mikan',
                    'banana' => 'banana',
                )
            ))
        );
        $this->assertEquals(
            'array(2) {
  ["title"]=>
  string(6) "fruits"
  ["hash"]=>
  array(3) {
    ["apple"]=>
    string(5) "ringo"
    ["orange"]=>
    string(5) "mikan"
    ["banana"]=>
    string(6) "banana"
  }
}
string(4) "hoge"
array(1) {
  ["fuga"]=>
  string(4) "piyo"
}
',
            d(array(
                'title' => 'fruits',
                'hash'  => array(
                    'apple'  => 'ringo',
                    'orange' => 'mikan',
                    'banana' => 'banana',
                )
            ),
            'hoge',
            array(
                'fuga' => 'piyo',
            ))
        );
    }


}
