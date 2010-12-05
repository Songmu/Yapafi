<?php
set_include_path(get_include_path().PATH_SEPARATOR.'../extlib');
require_once 'DataValidator/Base.php'; //
require_once 'DataValidator/Japanese.php';


class DataValidator_JapaneseTest extends PHPUnit_Framework_TestCase{
    
    public function testJISX0208(){
        $v = new DataValidator_Japanese();
        
        $ok = array(
            'あいうえお',
            'カキクケコ',
            '阿吽',
            '#"!$&',
         );
        
        foreach ( $ok as $val ){
            $this->assertTrue($v->checkJISX0208($val));
        }
        
        $ng = array(
            'ｶｷｸｹｺ',
            '髙',
        );
        
        foreach ( $ng as $val ){
            $this->assertFalse($v->checkJISX0208($val));
        }
    }

    public function testJAPANESE(){
        $v = new DataValidator_Japanese();
        
        $ok = array(
            'あいうえお',
            'カキクケコ',
            '阿吽',
            '#"!$&',
            'ｶｷｸｹｺ',
            '髙',
         );
        
        foreach ( $ok as $val ){
            $this->assertTrue($v->checkJAPANESE($val));
        }
        
        $ng = array(
            '你好',
        );
        
        foreach ( $ng as $val ){
            $this->assertFalse($v->checkJAPANESE($val));
        }
    }
    
    
    public function testHIRAGANA(){
        $v = new DataValidator_Japanese();
        
        $ok = array(
            'あいうえお',
         );
        
        foreach ( $ok as $val ){
            $this->assertTrue($v->checkHIRAGANA($val));
        }
        
        $ng = array(
            'カキクケコ',
         );
        
        foreach ( $ng as $val ){
            $this->assertFalse($v->checkHIRAGANA($val));
        }
    }



}
