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
                'JAPANESE',
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

    public function testFormValidator4(){
        
        $post_data = array(
            'mail'  => 'test@example.com',
            'mail2' => 'test@example.com',
            'name'  => '松木雅幸',
            'age'   => '31',
        );
        
        $f_v = new FormValidator($post_data);
        $f_v->loadAllConstraint();
        
        $f_v->check(array(
            'mail'       => array('REQUIRED', 'EMAIL'),
            'mail mail2' => array('EQUALS'),
            'name'       => array(
                'REQUIRED',
                'JAPANESE',
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


    public function testFormValidator5(){
        
        $post_data = array(
            'mail'  => 'testexample.com',
            'mail2' => 'test@example.com',
            'name'  => '高兴！',
            'age'   => '201',
            'hoge'  => 'dee',
        );
        
        $f_v = new FormValidator($post_data);
        $f_v->loadAllConstraint();
        
        $f_v->check(array(
            'mail'       => array('REQUIRED', 'EMAIL'),
            'mail mail2' => array('EQUALS'),
            'name'       => array(
                'REQUIRED',
                'JAPANESE',
                array('MB_LENGTH', 0, 10),
            ),
            'age'       => array(
                array('BETWEEN', 20, 200),
            ),
            'hoge'      => array(
                array('REGEX', '/^\d{3}$/'),
            ),
        ));
        
        $f_v->setErrorMessages(array(
            'mail.REQUIRED'     => 'メールアドレスを入力してください',
            'mail.EMAIL'        => 'メールアドレスの入力形式をご確認下さい',
            'mail mail2.EQUALS' => 'メールアドレスと確認欄が一致していません',
            'name.REQUIRED'     => 'お名前を入力してください',
            'name.JAPANESE'     => 'お名前は日本語で入力してください',
            'age.BETWEEN'       => '年齢は20歳以上200歳以下で入力してください',
            'hoge.REGEX'        => 'hogeは数字3桁で入力してください',
        ));
        
        $this->assertTrue($f_v->hasError());
        
        $this->assertEquals(
            5,
            count($f_v->getErrors())
        );
        
        $this->assertEquals(
            'メールアドレスの入力形式をご確認下さい',
            (string)$f_v->getErrorMessage('mail')
        );
        
        $this->assertEquals(
            'メールアドレスと確認欄が一致していません',
            (string)$f_v->getErrorMessage('mail mail2')
        );

        $this->assertEquals(
            'お名前は日本語で入力してください',
            (string)$f_v->getErrorMessage('name')
        );
        
        $this->assertEquals(
            '年齢は20歳以上200歳以下で入力してください',
            (string)$f_v->getErrorMessage('age')
        );
        
        $this->assertEquals(
            'hogeは数字3桁で入力してください',
            (string)$f_v->getErrorMessage('hoge')
        );
        
        
    }

}
