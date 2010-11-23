<?php
// This module port from Perl's Mail::Address::MobileJp
class Mail_Address_MobileJp{
    private $regex_mobile = '^(?:
    dct\.dion\.ne\.jp|
    tct\.dion\.ne\.jp|
    hct\.dion\.ne\.jp|
    kct\.dion\.ne\.jp|
    cct\.dion\.ne\.jp|
    sct\.dion\.ne\.jp|
    qct\.dion\.ne\.jp|
    oct\.dion\.ne\.jp|
    email\.sky\.tdp\.ne\.jp|
    email\.sky\.kdp\.ne\.jp|
    email\.sky\.cdp\.ne\.jp|
    sky\.tu\-ka\.ne\.jp|
    cara\.tu\-ka\.ne\.jp|
    sky\.tkk\.ne\.jp|
    .*\.sky\.tkk\.ne\.jp|
    sky\.tkc\.ne\.jp|
    .*\.sky\.tkc\.ne\.jp|
    email\.sky\.dtg\.ne\.jp|
    em\.nttpnet\.ne\.jp|
    .*\.em\.nttpnet\.ne\.jp|
    cmchuo\.nttpnet\.ne\.jp|
    cmhokkaido\.nttpnet\.ne\.jp|
    cmtohoku\.nttpnet\.ne\.jp|
    cmtokai\.nttpnet\.ne\.jp|
    cmkansai\.nttpnet\.ne\.jp|
    cmchugoku\.nttpnet\.ne\.jp|
    cmshikoku\.nttpnet\.ne\.jp|
    cmkyusyu\.nttpnet\.ne\.jp|
    pdx\.ne\.jp|
    d.\.pdx\.ne\.jp|
    wm\.pdx\.ne\.jp|
    phone\.ne\.jp|
    .*\.mozio\.ne\.jp|
    page\.docomonet\.or\.jp|
    page\.ttm\.ne\.jp|
    pho\.ne\.jp|
    moco\.ne\.jp|
    emcm\.ne\.jp|
    p1\.foomoon\.com|
    mnx\.ne\.jp|
    .*\.mnx\.ne\.jp|
    ez.\.ido\.ne\.jp|
    cmail\.ido\.ne\.jp|
    .*\.i\-get\.ne\.jp|
    willcom\.com
    )$';

    private $regex_imode = '^(?:
    docomo\.ne\.jp
    )$';

    private $regex_softbank = '^(?:
    jp\-[dhtckrnsq]\.ne\.jp|
    [dhtckrnsq]\.vodafone\.ne\.jp|
    softbank\.ne\.jp|
    disney.ne.jp
    )$';

    private $regex_ezweb = '^(?:
    ezweb\.ne\.jp|
    .*\.ezweb\.ne\.jp
    )$';
    
    private static $instance = null;
    private function __construct(){
        
    }

    public static function getChecker(){
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    function is_imode($addr) {
        $domain = self::_domain($addr);
        return $domain && preg_match("!$this->regex_imode!x", $domain);
    }

    function is_softbank($addr){
        $domain = self::_domain($addr);
        return $domain && preg_match("!$this->regex_softbank!x", $domain);
    }

    function is_vodafone($addr) {
        return $this->is_softbank($addr);
    }

    function is_ezweb($addr) {
        $domain = self::_domain($addr);
        return $domain && preg_match("!$this->regex_ezweb!x", $domain);
    }

    function is_mobile_jp($addr) {
        $domain = self::_domain($addr);
        return $domain && preg_match(
            "!(?:
                $this->regex_imode   |
                $this->regex_softbank|
                $this->regex_ezweb   |
                $this->regex_mobile
            )!x", $domain);
    }

    static function _domain($stuff) {
        $i = strrpos($stuff, '@');
        return $i !== false ? substr($stuff, $i + 1) : false;
    }


}
