<?php
// Yapafi - Yet Another PHP Application Frawework Interface
// Author:  Masayuki Matsuki
// Version: 0.01
// パス情報やファイル情報など即値が多いので、余裕があれば見直したい。(規約と言い切るという手もあるが…)
set_include_path(get_include_path().PATH_SEPARATOR.'lib/'.PATH_SEPARATOR.'view/');
include_once "yapafi.ini"; // session_error(), not_found()を定義
error_reporting(ERROR_LEVEL);
set_error_handler('exeption_error_handler', ERROR_LEVEL);
include_once "app.ini";

try{
    if ( mb_eregi('yapafi\.php', $_SERVER['REQUEST_URI'] ) ){
        // yapafi.php/pathinfo みたいなURLにアクセスがあった場合に弾く
        header("HTTP/1.1 404 Not Found");
        not_found();
        exit;
    }

    //PATH_INFOからコントローラ名を取得する
    $cntl_name = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : 'index';
    $cntl_name = mb_ereg_replace('^/','',$cntl_name);
    $cntl_name = strtolower($cntl_name); // 全部小文字に(URLは基本的に小文字のみの前提。というかケースインセンシティヴ)

    // $cntl_nameが正しい(制約に沿っている)かどうかのチェック。
    if (
        mb_ereg('\.', $cntl_name)          || // ドット含むとダメ(ディレクトラトラバーサル対策)
        mb_ereg('^\d', $cntl_name)         || // 数字で始まるとダメ(規約)
        mb_ereg('[^_a-z0-9]', $cntl_name ) || // 英字小文字・数字・アンスコ以外が含まれるとダメ(規約)
        (  !file_exists( 'app/'.$cntl_name.'.php' ) && //コントローラファイルが無くて
           !file_exists( $cntl_name.'.tmpl' )  )       //かつ、直下にビューファイルも無い場合 404
    ){
        header("HTTP/1.1 404 Not Found");
        not_found();
        exit;
    }

    // コントローラーが無い場合、'/' 以下のビューを直接呼び出す。
    // (この機能は要らない? 普通に phpファイルを置けば動くので。ただセッションファイル等の設定を共有したいときなんかは必要かも）
    if ( !file_exists( 'app/'.$cntl_name.'.php' ) ){
        require_once $cntl_name.'.tmpl';
        exit;
    }

    // コントローラー名から規約に則って、ファイルの読み込みとオブジェクトの作成を行う
    // PATH_INFOの情報がそのままファイル名にマッピングされる。
    require_once 'app/'.$cntl_name.'.php';

    // PATH_INFOのcamelizeを行い、スラッシュをアンスコに変換、最後に'_c'を加えたのがクラス名になる。
    // (最後に'_c'を付けるのは別のモジュールとクラス名のバッティングを防ぐため。名前空間が使えると良いんですけどね…。)
    $cntl_name = str_replace(' ','',ucwords(str_replace('_',' ',$cntl_name))); //camelize
    $cntl_name = mb_ereg_replace('/','_',$cntl_name);
    $cntl_name .= '_c';

    $obj = new $cntl_name();
    $obj->init();
    
    if( $obj->sessionCheck() ){
        $obj->run();
        // CSVダウンロードやリダイレクトさせるだけの機能もあるはずなので、
        // かならずViewをセットさせる設計は筋悪だと考え直し中。
        // 後、自動的にテンプレートは呼んで良いんじゃないかとかも考え中。(もちろん差し替えは可能なように)
        
        $response_body = render($obj->getView(), $obj->view_args ); //$obj->render とかの方が良いかも？
        
        $obj->setHeader();
        echo $response_body;
        
    }
    else{
        header("HTTP/1.1 400 Bad Request");
        session_error();
        exit;
    }
}
catch( Exception $ex ){
    header("HTTP/1.1 500 Internal Server Error");
    if ( YAPAFI_DEBUG ){
        throw $ex;
    }
    else {
        logging( $ex->getMessage() );
        try{
            internal_server_error();
        }
        catch( Exception $ex ){
            echo 'INTERNAL SERVER ERROR';
        }
        exit;
    }
}

/* Yapafi_Controller
   ベースとなるコントローラクラス。
   このクラスを継承し、各メソッドをオーバーライドすることで
   URLの挙動を振り分ける。
   このクラスを直接継承するよりも、プロジェクト毎にベースのクラスを作り、
   それを各コントローラーで継承するのがベタープラクティス
*/

abstract class Yapafi_Controller{
    private $view_filename;
    public  $view_args;
    
    function init(){
        session_start();
    }
    
    // 共通ヘッダを差し込む。ファイルダウンロード時等はオーバーライドして使用する
    function setHeader(){
        header('Content-Type: text/html; charset=utf-8 ;Content-Language: ja');
        set_no_cache();
    }
    
    // セッションチェックを行う。falseが返ったら400 Bad Requestをクライアントに返す。
    // Session Beanの持ち方がプロジェクトごとに違うと思うので、プロジェクトのベースコントローラでセッションの前提
    // 条件を記載しておくと楽。
    function sessionCheck() {
        return true;
    }
    
    //ここがメインロジック。各コントローラで実装する。内部で必ずsetViewを呼び出してViewをセットする必要がある。
    //↑この設計はちょっと疑問。RedirectやファイルDLのハンドリングをどうするか？
    abstract function run();
    
    
    function setView($filename, $tmpl_array = array()){
        //$stashにイテレータを入れたりした場合など、一括処理が上手く行かないが、
        //$stashへのオブジェクトのセットは無しの方向で。やる場合は自己責任で。
        $tmpl_array = Yapafi_Controller::_deep_htmlspecialchars( $tmpl_array ); //一括エスケープ
        if ( mb_internal_encoding() != OUTPUT_ENCODING ){ //一括エンコーディング
            $tmpl_array = Yapafi_Controller::_deep_mb_convert_encoding( 
                $tmpl_array, OUTPUT_ENCODING, mb_internal_encoding()
            );
        }
        $this->view_args = $tmpl_array;
        $this->view_filename = $filename;
    }
    
    final function getView(){
        if( !$this->view_filename ){
            //throw new YapafiException('You should set template filename by using [setView] method!');
            $view = get_class($this);
            $view = mb_ereg_replace('_c$','',$view);
            $view = mb_ereg_replace('_','/',$view);
            $view = ltrim(preg_replace('/([A-Z])/e',"'_'.strtolower('$1')",$view),'_');
            $view = mb_ereg_replace('^_','',$view);
            return $view . '.tmpl';
        }
        return $this->view_filename;
    }
    
    
    static function _deep_mb_convert_encoding( $arr, $enc_to, $enc_from ){
        if( is_array( $arr ) ){
            foreach ( $arr as $k => $v ){
                if( is_array($v) ){
                    $arr[$k] = self::_deep_mb_convert_encoding( $arr[$k], $enc_to, $enc_from );
                }
                else{
                    if ( is_a( $v, 'RawString') ){
                        $arr[$k]->str = mb_convert_encoding( $v, $enc_to, $enc_from );
                    }
                    elseif( !is_object( $v ) ){
                        $arr[$k] = mb_convert_encoding( $v, $enc_to, $enc_from );
                    }
                }
            }
        }
        else{
            if ( is_a( $arr, 'RawString' ) ){
                $arr->str = mb_convert_encoding( $arr, $enc_to, $enc_from );
            }
            elseif ( !is_object( $arr ) ){
                $arr = mb_convert_encoding( $arr, $enc_to, $enc_from );
            }
        }
        return $arr;
    }
    
    static function _deep_htmlspecialchars( $arr ){
        if( is_array( $arr ) ){
            foreach ( $arr as $k => $v ){
                if( is_array($v) ){
                    $arr[$k] = self::_deep_htmlspecialchars( $arr[$k] );
                }
                else{
                    if ( !is_object( $arr[$k] ) ){
                        $arr[$k] = htmlspecialchars( $v, ENT_QUOTES );
                    }
                }
            }
        }
        else{
            if ( !is_object( $arr ) ){
                $arr = htmlspecialchars( $arr, ENT_QUOTES );
            }
        }
        return $arr;
    }
    
    
}

class YapafiException extends Exception {}

function render($filename, $args = array()){
    $stash = $args;
    ob_start();
    require $filename;
    $str = ob_get_contents();
    ob_end_clean();
    return $str;
}

function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

function logging($str){
    $traces = debug_backtrace();
    $trace  = $traces[0];
    $now = getDate();
    file_put_contents(
        YAPAFI_LOG_PATH . 'app'.date('Ymd', $now[0]).'.log',
        '['. date('H:i:s', $now[0]).'] '. $str . ' at ' .$trace['file'].' line '. $trace['line'] . "\n",
        FILE_APPEND | LOCK_EX
    );
}

function is_post_request(){
    return $_SERVER["REQUEST_METHOD"] == "POST";
}

function redirect($url, $response_code = '303'){
    $msgs = array(
        '301'   => 'Moved Permanently',
        '302'   => 'Found',
        '303'   => 'See Other',
        '307'   => 'Temporary Redirect',
    );
    
    header('HTTP/1.1 '.$response_code.' '.$msgs[$response_code]);
    header("Location: $url");//本当は絶対URLに書き換えたい。
    exit();
}

function logout(){
    $_SESSION = array();
    // セッションを切断するにはセッションクッキーも削除する。
    // Note: セッション情報だけでなくセッションを破壊する。
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    // 最終的に、セッションを破壊する
    session_destroy();
}

// これだと一時ファイルをダウンロードさせる時などに対応できない。
function download_file( $file, $file_name, $mime_type = 'text/plain', $charset = '' ){
    $handle = fopen($file, 'r');
    $contents = fread($handle, filesize($file));
    fclose($handle);
    
    download_data( $contents, $file_name, $mime_type, $charset );
}

function download_data( $data, $file_name, $mime_type = 'text/plain', $charset = '' ){
    set_no_cache();
    $content_type = "$mime_type";
    if ( $charset ){
        $content_type .= '; charset='. $charset;
    }
    header('Content-Type: '.$content_type);
    header('Content-Disposition: attachment; filename="'.$file_name.'"');
    
    echo $data;
    exit;
}


function set_no_cache(){
    header("Expires: Thu, 01 Dec 1994 16:00:00 GMT");
    header("Last-Modified: ". gmdate("D, d M Y H:i:s"). " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
}

/* テンプレートセットする文字列を自動エスケープされたくない場合は以下の関数で文字列をラッピングしておく */
function raw_str( $str ){
    return new RawString($str);
}
class RawString {
    public $str;
    function __construct($str) {
        $this->str = $str;
    }
    function __toString() {
        return $this->str;
    }
}

function exeption_error_handler( $err_no, $errstr, $errfile, $errline ){
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}



?>