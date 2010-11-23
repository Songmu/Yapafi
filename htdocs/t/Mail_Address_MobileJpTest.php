<?php
require_once '../extlib/Mail/Address/MobileJp.php';

class Mail_Address_MobileJpTest extends PHPUnit_Framework_TestCase{

    public function testMailAddressMobileJp(){
        $c = Mail_Address_MobileJp::getChecker();
        $ok_imode = array(
            'foo@docomo.ne.jp',
        );

        $ok_vodafone = array(
            'foo@jp-d.ne.jp',
            'foo@d.vodafone.ne.jp',
            'foo@softbank.ne.jp',
        );

        $ok_ezweb = array(
            'foo@ezweb.ne.jp',
            'foo@hoge.ezweb.ne.jp',
        );

        $ok_softbank = array(
            'foo@softbank.ne.jp',
            'foo@d.vodafone.ne.jp',
            'foo@disney.ne.jp',
        );

        $ok_mobile = array(
            'foo@mnx.ne.jp',
            'foo@bar.mnx.ne.jp',
            'foo@dct.dion.ne.jp',
            'foo@sky.tu-ka.ne.jp',
            'foo@bar.sky.tkc.ne.jp',
            'foo@em.nttpnet.ne.jp',
            'foo@bar.em.nttpnet.ne.jp',
            'foo@pdx.ne.jp',
            'foo@dx.pdx.ne.jp',
            'foo@phone.ne.jp',
            'foo@bar.mozio.ne.jp',
            'foo@p1.foomoon.com',
            'foo@x.i-get.ne.jp',
            'foo@ez1.ido.ne.jp',
            'foo@cmail.ido.ne.jp',
        );

        $ok_mobile += $ok_imode + 
            $ok_vodafone +
            $ok_ezweb    +
            $ok_softbank;

        $not = array(
            'foo@example.com',
            'foo@dxx.pdx.ne.jp',
            'barabr',
        );

        foreach ($ok_imode as $ok) {
            $this->assertTrue( $c->is_imode($ok) );
        }

        foreach ($ok_vodafone as $ok) {
            $this->assertTrue( $c->is_vodafone($ok) );
        }

        foreach ($ok_ezweb as $ok) {
            $this->assertTrue( $c->is_ezweb($ok) );
        }

        foreach ($ok_softbank as $ok) {
            $this->assertTrue( $c->is_softbank($ok) );
        }

        foreach ($ok_mobile as $ok) {
            $this->assertTrue( $c->is_mobile_jp($ok) );
        }

        foreach ($not as $no) {
            $this->assertFalse( $c->is_mobile_jp($no) );
        }
        
    }



}
