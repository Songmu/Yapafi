<?php
set_include_path(get_include_path().PATH_SEPARATOR.'../extlib');
require_once 'FormValidator.php';

class FormValidatorTest extends PHPUnit_Framework_TestCase{
    
    public function testFormValidator(){
        
        $post_data = array(
            'mail'  => 'test@example.com',
            'mail2' => 'test@example.com',
            'name'  => '松木雅幸',
            'age'   => '30',
            'hoge'  => 'ddd',
        );
        
        $f_v = new FormValidator($post_data);
        $f_v->loadConstraint('DataValidator_Web', 'DataValidator_Japanese');
        
        $f_v->check(array(
            'mail'       => array('REQUIRED', 'EMAIL'),
            'mail mail2' => array('EQUALS'),
            'name'       => array(
                'REQUIRED',
                array('MB_LENGTH', 0, 10),
            ),
            'age'       => array(
                array('BETWEEN', 20, 200),
            ),
            'hoge'      => array(
                array('REGEX', '/^\d{3}$/'),
            ),
        ));
        
        
        $this->assertTrue($f_v->hasError());
        $this->assertTrue($f_v->isError('hoge'));
        $this->assertFalse($f_v->isError('age'));
        
        $this->assertEquals(
            'hogeの入力形式が正しくありません',
            (string)$f_v->getErrorMessage('hoge')->assign('hoge')
        );
        
    }

    public function testFormValidator2(){
        
        $post_data = array(
            'mail'  => 'test@example.com',
            'mail2' => 'test@example.comm',
            'name'  => '松木雅幸',
            'age'   => '1',
            'hoge'  => '111',
        );
        
        $f_v = new FormValidator($post_data);
        $f_v->loadConstraint('DataValidator_Web', 'DataValidator_Japanese');
        
        $f_v->check(array(
            'mail'       => array('REQUIRED', 'EMAIL'),
            'mail mail2' => array('EQUALS'),
            'name'       => array(
                'REQUIRED',
                array('MB_LENGTH', 0, 10),
            ),
            'age'       => array(
                array('BETWEEN', 20, 200),
            ),
            'hoge'      => array(
                array('REGEX', '/^\d{3}$/'),
            ),
        ));
        
        
        $this->assertTrue($f_v->hasError());
        $this->assertTrue($f_v->isError('mail mail2'));
        $this->assertTrue($f_v->isError('age'));
        
        
    }
    
    public function testFormValidator3(){
        
        $post_data = array(
            'mail'  => 'test@example.com',
            'mail2' => 'test@example.com',
            'name'  => '松木雅幸',
            'age'   => '31',
        );
        
        $f_v = new FormValidator($post_data);
        $f_v->loadConstraint('DataValidator_Web', 'DataValidator_Japanese');
        
        $f_v->check(array(
            'mail'       => array('REQUIRED', 'EMAIL'),
            'mail mail2' => array('EQUALS'),
            'name'       => array(
                'REQUIRED',
                array('MB_LENGTH', 0, 10),
            ),
            'age'       => array(
                array('BETWEEN', 20, 200),
            ),
            'hoge'      => array(
                array('REGEX', '/^\d{3}$/'),
            ),
        ));
        
        $this->assertFalse($f_v->hasError());
        
        
    }



}
