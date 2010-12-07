<?php
require_once 'FormValidator.php'; // FormValidatorクラスをロードします。
class Sample_Form_c extends Yapafi_Controller {
    protected $allow_exts = array( '', 'api');
    
    function runGet() {
        $error_msgs = isset($_SESSION['error_msgs']) ? $_SESSION['error_msgs'] : array();
        $msg        = isset($_SESSION['msg'])        ? $_SESSION['msg']        : '';
        $form_input = isset($_SESSION['form_input']) ? $_SESSION['form_input'] : array();
        
        unset($_SESSION['form_input']);
        unset($_SESSION['msg']);
        unset($_SESSION['error_msgs']);
        
        $this->stash = array(
            'msg'        => $msg,
            'error_msgs' => $error_msgs,
            'form_input' => $form_input,
        );
    }
    
    function runPost() {
        // validatorをロードします。
        $validator = new FormValidator($_POST);
        
        // 必要な、DataValidatorクラスをロードします。(標準ではDataValidator_Defaultクラスのみロードされています)
        // 全ての制約をロードしたい場合は、loadAllConstraint()メソッドを使用します。
        // また、独自の制約クラスを作成して、それをロードすることも出来ます。
        $validator->loadConstraint(
            'DataValidator_Web',
            'DataValidator_Japanese'
        );
        
        // 制約を以下のように指定します。
        $validator->check(array(
            'name'          => array(
                'REQUIRED',
                'JISX0208',
            ),
            'mail'          => array(
                'REQUIRED',
                'EMAIL_LOOSE',
            ),
            'mail mail2'    => array('EQUALS',),
            'address'       => array('JAPANESE',),
            'age'           => array(
                'UINT',
                array('BETWEEN', 20, 150),
            ),
            'tel'        => array('JTEL',),
            'gender'     => array(
                array('CHOICE', array('1','2','3') ),
            ),
            'secret_num' => array(
                'REQUIRED',
                // ↓正規表現を指定します。パターンを複数指定することも可能です（その場合全ての正規表現にマッチするか）
                array('REGEX', '/^\d{5}-\d{4}$/'),
                // array('REGEX_ANY', '/\d{4}/', '/aaaa/'); //この場合は正規表現どれかにマッチすればOK
                // array('!REGEX', '/^\d{5}-d{4}$/'); // ルールの頭に"!"をつけることで評価を反転させられます
            )
        ));
        
        // エラーメッセージを以下のように指定します( 項目名 . 制約名 )
        $validator->setErrorMessages(array(
            'name.REQUIRED'       => 'お名前を入力してください',
            'name.JISX0208'       => 'お名前はJIS第一・第二水準文字で入力してください',
            'mail.REQUIRED'       => 'メールアドレスを入力してください',
            'mail.EMAIL_LOOSE'    => 'メールアドレスの形式をご確認下さい',
            'mail mail2.EQUALS'   => 'メールアドレスと確認欄には同じメールアドレスを入力してください',
            'address.JAPANESE'    => '住所は日本語で入力してください',
            'age.UINT'            => '年齢を数値で入力してください',
            'age.BETWEEN'         => '年齢は20以上150以下で入力してください',
            'tel.JTEL'            => '電話番号の形式をご確認下さい',
            'gender.CHOICE'       => 'リクエストが不正です',
            'secret_num.REQUIRED' => '秘密の番号を入力してください',
            'secret_num.REGEX'    => '秘密の番号は「数字5桁 ハイフン 数字4桁」で入力してください',
        ));
        // 制約一覧やエラーメッセージ一覧は、プログラム内に記述しないで、JSONなりYAMLなりで外部ファイルに持たせても良いと思います。
        
        // エラーメッセージを取得します。なければ空の配列が返ってきます。
        // 単にエラーの有無だけを知りたいときは、hasError() isValid()メソッドを使いましょう。
        $error_msgs = $validator->getErrorMessages();
        
        if ( $this->ext === 'api' ){ //JavaScriptで値の検証をしたい場合はXHRを飛ばして、検証を行うなんてことも可能かと思います。
            return json_encode($error_msg);
        }
        
        if ( $validator->isValid() ){
            $_SESSION['msg'] = 'フォームの投稿が完了しました。(嘘)';
        }
        else{
            $_SESSION['error_msgs'] = $error_msgs;
            $_SESSION['msg'] = 'フォームの投稿に失敗しました。';
            $_SESSION['form_input'] = $_POST;
        }
        
        redirect('./form');
    }
    
    
}

