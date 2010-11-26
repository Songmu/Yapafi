<?php
require 'model/bbs.php';
class Sample_Bbs_c extends Yapafi_Controller {
    function runGet() {
        $msg = '';
        if ( isset( $_SESSION['_msg'] ) ){
            $msg = $_SESSION['_msg'];
            unset($_SESSION['_msg']);
        }
        $_SESSION['_token'] = get_token();
        $this->stash = array(
            'title'     => '一行掲示板',
            'comments'  => Model_Bbs::getComments(),
            '_token'    => $_SESSION['_token'],
            '_msg'      => $msg,
        );
    }
    
    function runPost() {
        if ($_POST['_token'] !== $_SESSION['_token'] ){
            $_SESSION['_msg'] = '不正なアクセスです。';
        }
        elseif ( !$_POST['name'] || !$_POST['comment']){
            $_SESSION['_msg'] = 'お名前とコメントを入力してください。';
        }
        else{
            try{
                Model_Bbs::update($_POST['name'], $_POST['comment']);
                $_SESSION['_msg'] = 'コメントを投稿しました';
            }
            catch( Exception $ex ){
                $_SESSION['_msg'] = '更新に失敗しました。';
                logging($ex->getMessage());
            }
        }
        redirect('bbs');
    }
}
