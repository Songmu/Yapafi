<?php
class DataValidator_Japanese extends DataValidator_Base {
    protected $error_messages = array(
        'HIRAGANA'                  => '[_1]はひらがなで入力してください',
        'KATAKANA'                  => '[_1]は全角カタカナで入力してください',
        'ZENKAKU'                   => '[_1]は全角で入力してください',
        'HANKAKU_KATAKANA'          => '[_1]は半角カタカナで入力してください',
        'JISX0208'                  => '[_1]の入力文字に機種依存文字が含まれています',
        'JAPANESE'                  => '[_1]は日本語で入力してください',
        'NO_ZENKAKU_MARK_STRICT'    => '[_1]に入力できない文字が含まれています',
        'NO_ZENKAKU_MARK'           => '[_1]に入力できない文字が含まれています',
        'NO_ZENKAKU_MARK_LOOSE'     => '[_1]に入力できない文字が含まれています',
        'JTEL'                      => '電話番号の形式が不正です',
        'JZIP'                      => '郵便番号の形式が不正です',
    );
    
    function checkHIRAGANA($val){
        return (bool)preg_match( '/\A[ぁ-ゖー　\s]+\z/u', $val );
    }
    
    function checkKATAKANA($val){
        return (bool)preg_match( '/\A[ァ-ヺー　\s]+\z/u', $val);
    }
    
    
    function checkZENKAKU($val){
        return !preg_match('/[\x00-\x7Fｦ-ﾟ]/', $val);
    }
    
    
    function checkHANKAKU_KATAKANA($val){
        return (bool)preg_match('/\A[ｦ-ﾟ ]\Z/', $val);
    }
    
    
    function checkJISX0208($val){
        if ( preg_match('/[\x00-\x20\x7F]/', $val) ){
            return false;
        }
        // JIS X 0201のラテン文字等も含んでしまうが、まあOKか？
        // 嫌だったら、ZENKAKU とあわせてチェックしましょう。
        return $val === mb_convert_encoding( 
            mb_convert_encoding($val, 'ISO-2022-JP', 'UTF-8'),
            'UTF-8',
            'ISO-2022-JP'
        );
    }
    
    // 乱暴だけど。これで。
    function checkJAPANESE($val){
        return $val === mb_convert_encoding( 
            mb_convert_encoding($val, 'SJIS-win', 'UTF-8'),
            'UTF-8',
            'SJIS-win'
        );
    }
    
    // 全角記号は ー― (長音・全角ハイフン)除いて完全に弾く(住所とか)
    // ref. http://www.officek.jp/skyg/wn/doc/kishuizon.shtml
    // ref. http://j-truck.net/help.cgi?type=%95%5C%8E%A6&docfile=08_dbcsmark.htm&word=
    function checkNO_ZENKAKU_MARK_STRICT(){
        return !preg_match('/[、。，．・：；？！゛゜´｀¨＾￣＿ヽヾゝゞ〃仝々〆〇‐／＼～∥｜…‥‘’“”（）〔〕［］｛｝〈〉《》「」『』【】＋－±×÷＝≠＜＞≦≧∞∴♂♀°′″℃￥＄￠￡％＃＆＊＠§☆★○●◎◇◆□■△▲▽▼※〒→←↑↓〓∈∋⊆⊇⊂⊃∪∩∧∨￢⇒⇔∀∃∠⊥⌒∂∇≡≒≪≫√∽∝∵∫∬Å‰♯♭♪†‡¶◯─│┌┐┘└├┬┤┴┼━┃┏┓┛┗┣┳┫┻╋┠┯┨┷┿┝┰┥┸╂ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩαβγδεζηθικλμνξοπρστυφχψωАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя①②③④⑤⑥⑦⑧⑨⑩⑪⑫⑬⑭⑮⑯⑰⑱⑲⑳ⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩ�㍉㌔㌢㍍㌘㌧㌃㌶㍑㍗㌍㌦㌣㌫㍊㌻㎜㎝㎞㎎㎏㏄㎡㍻〝〟№㏍℡㊤㊥㊦㊧㊨㈱㈲㈹㍾㍽㍼≒≡∫∮∑√⊥∠∟⊿∵∩∪]/', $val);
    }
    
    // 次の全角記号は許容する
    // 、。，．・：；？！゛＿々〃〆〇ー／＼～｜…‘’“”（）［］｛｝「」『』【】＋－±×÷＝≠＜＞￥＄％＃＆＊＠
    function checkNO_ZENKAKU_MARK(){
        return !preg_match('/[゜´｀¨＾￣ヽヾゝゞ仝―‐∥‥〔〕〈〉《》≦≧∞∴♂♀°′″℃￠￡§☆★○●◎◇◆□■△▲▽▼※〒→←↑↓〓∈∋⊆⊇⊂⊃∪∩∧∨￢⇒⇔∀∃∠⊥⌒∂∇≡≒≪≫√∽∝∵∫∬Å‰♯♭♪†‡¶◯─│┌┐┘└├┬┤┴┼━┃┏┓┛┗┣┳┫┻╋┠┯┨┷┿┝┰┥┸╂ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩαβγδεζηθικλμνξοπρστυφχψωАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя①②③④⑤⑥⑦⑧⑨⑩⑪⑫⑬⑭⑮⑯⑰⑱⑲⑳ⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩ�㍉㌔㌢㍍㌘㌧㌃㌶㍑㍗㌍㌦㌣㌫㍊㌻㎜㎝㎞㎎㎏㏄㎡㍻〝〟№㏍℡㊤㊥㊦㊧㊨㈱㈲㈹㍾㍽㍼≒≡∫∮∑√⊥∠∟⊿∵∩∪]/', $val);
    }
    
    // 機種依存文字になりがちな全角文字以外は最大限許容する
    // ref. http://cha.sblo.jp/article/19201205.html
    function checkNO_ZENKAKU_MARK_LOOSE(){
        return !preg_match('/[ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩαβγδεζηθικλμνξοπρστυφχψωАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя①②③④⑤⑥⑦⑧⑨⑩⑪⑫⑬⑭⑮⑯⑰⑱⑲⑳ⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩ�㍉㌔㌢㍍㌘㌧㌃㌶㍑㍗㌍㌦㌣㌫㍊㌻㎜㎝㎞㎎㎏㏄㎡㍻〝〟№㏍℡㊤㊥㊦㊧㊨㈱㈲㈹㍾㍽㍼≒≡∫∮∑√⊥∠∟⊿∵∩∪]/', $val);
    }
    
    function checkJTEL($val){
        return (bool)preg_match('/\A0\d+-?\d+-?\d+\z/', $val);
    }
    
    function checkJZIP($val){
        if ( is_array($val) ){
            $val = $val[0] . '-' . $val[1];
        }
        return (bool)preg_match('/\A\d{3}-\d{4}\z/', $val);
    }
    
    
}
