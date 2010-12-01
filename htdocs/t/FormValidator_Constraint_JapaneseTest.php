<?
set_include_path(get_include_path().PATH_SEPARATOR.'../extlib');
require_once 'FormValidator.php';
require_once 'FormValidator/Constraint/Japanese.php';


class FormValidator_Constraint_JapaneseTest extends PHPUnit_Framework_TestCase{
    
    public function testJISX0208(){
        $v = new FormValidator_Constraint_Japanese();
        
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

    public function testCP932COMPATIBLE(){
        $v = new FormValidator_Constraint_Japanese();
        
        $ok = array(
            'あいうえお',
            'カキクケコ',
            '阿吽',
            '#"!$&',
            'ｶｷｸｹｺ',
            '髙',
         );
        
        foreach ( $ok as $val ){
            $this->assertTrue($v->checkCP932COMPATIBLE($val));
        }
        
        $ng = array(
            '你好',
        );
        
        foreach ( $ng as $val ){
            $this->assertFalse($v->checkCP932COMPATIBLE($val));
        }
    }
    
    public function testJAPANESE(){
        $v = new FormValidator_Constraint_Japanese();
        
        $ok = array(
            'あいうえお',
            'カキクケコ',
            '阿吽',
            '#"!$&',
            //'ｶｷｸｹｺ',
            '髙',
         );
        
        foreach ( $ok as $val ){
            $this->assertTrue($v->checkJAPANESE($val));
        }
        
    }
    
    public function testHIRAGANA(){
        $v = new FormValidator_Constraint_Japanese();
        
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
