<?php
// Yapafi - Yet Another PHP Application Frawework Interface
// Author:  Masayuki Matsuki
// Version: 0.01
// パス情報やファイル情報など即値が多いので、余裕があれば見直したい。(規約と言い切るという手もあるが…)
set_include_path(get_include_path().PATH_SEPARATOR.'lib/'.PATH_SEPARATOR.'view/');
include_once "yapafi.ini"; // session_error(), not_found()を定義
error_reporting(YAPAFI_ERROR_LEVEL);
set_error_handler('exeption_error_handler', YAPAFI_ERROR_LEVEL);
include_once "app.ini";

try{
    if ( preg_match('/yapafi\.php/i', $_SERVER['REQUEST_URI'] ) ){
        // yapafi.php/pathinfo みたいなURLにアクセスがあった場合に弾く
        header("HTTP/1.1 404 Not Found");
        not_found();
        exit;
    }
    
    //PATH_INFOからコントローラ名を取得する
    $cntl_name = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : 'index';
    $cntl_name = preg_replace('!^/!','',$cntl_name); // 頭のスラッシュを削除
    $cntl_name = strtolower($cntl_name); // 全部小文字に(URLは基本的に小文字のみの前提。というかケースインセンシティヴ)

    $args = array();
    if ( preg_match( '!/$!', $cntl_name ) ){
        $cntl_name = preg_replace( '!/$!', '', $cntl_name );
        $args[] = '';
    }

    // $cntl_nameが正しい(制約に沿っている)かどうかのチェック。
    if (
        preg_match('/\\./', $cntl_name)          || // ドット含むとダメ(ディレクトラトラバーサル対策)
        preg_match('!^(\\d|/)!', $cntl_name)     || // 数字で始まるとダメ(規約) スラッシュで始まってもダメ。
        preg_match('!//!', $cntl_name)           || // スラッシュが連続するとダメ
        preg_match('![^_a-z0-9/]!', $cntl_name )    // 英字小文字・数字・アンスコ・スラッシュ以外が含まれるとダメ(規約)
    ){
        header("HTTP/1.1 404 Not Found");
        not_found();exit;
    }

    //コントローラファイルが無い場合
    if(  !file_exists( 'app/'.$cntl_name.'.php' ) ){
        if ( file_exists( $cntl_name.'.tpl' )  ){
            // '/' 以下にビューがある場合はビューを直接呼び出す。
            require_once $cntl_name.'.tpl'; exit;
        }
        else {
            $found_cntl = false;
            while ( preg_match('!^(.+)/([^/]+)$!', $cntl_name, $matches ) ){
                $cntl_name = $matches[1];
                array_unshift( $args, $matches[2] );
                if ( file_exists( 'app/'.$cntl_name.'.php' ) ){
                    $found_cntl = true;
                    break;
                }
            }
            if ( !$found_cntl ){
                header("HTTP/1.1 404 Not Found");
                not_found();exit;
            }
        }
    }

    // コントローラー名から規約に則って、ファイルの読み込みとオブジェクトの作成を行う
    // PATH_INFOの情報がそのままファイル名にマッピングされる。
    require_once 'app/'.$cntl_name.'.php';

    // PATH_INFOのcamelizeを行い、スラッシュをアンスコに変換、最後に'_c'を加えたのがクラス名になる。
    // (最後に'_c'を付けるのは別のモジュールとクラス名のバッティングを防ぐため。名前空間が使えると良いんですけどね…。)
    $cntl_name = str_replace(' ','',ucwords(str_replace('_',' ',$cntl_name))); //camelize
    $cntl_name = preg_replace('!/!','_',$cntl_name);
    $cntl_name .= '_c';
    // …いや実は別にクラス名変換とか必要ないんじゃないかとか思えてきた…。どうせ一つしか呼ばれないんだし。
    try {
        $obj = new $cntl_name($args);
    }
    catch ( YapafiException $ex ) {//引数チェックにミスると例外が投げられる
        header("HTTP/1.1 404 Not Found");
        not_found();exit;
    }
    
    $obj->init();
    
    if( $obj->sessionCheck() ){
        $obj->run();
        
        //$obj->render とかの方が良いか？
        $response_body = render($obj->getView(), $obj->stash ); 
        $obj->setHeader();
        header('Content-Length: '. strlen($response_body));
        echo $response_body;
        
    }
    else{
        header("HTTP/1.1 403 Forbidden");
        session_error();exit;
    }
}
catch( Exception $ex ){
    $output = ob_get_contents();
    ob_end_clean();
    header("HTTP/1.1 500 Internal Server Error");
    if ( YAPAFI_DEBUG ){
        echo $output;
        throw $ex;
    }
    else {
        logging( $ex->getMessage(), 'ERROR' );
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
    public  $stash = array();
    
    protected $has_args = 0;
    protected $args;
    
    function __construct( $args ){
        if ( count($args) > $this->has_args ){
            throw new YapafiException('args too many!');
        }
        $this->args = $args;
    }
    
    function init(){
        session_start();
    }
    
    // 共通ヘッダを差し込む。ファイルダウンロード時等はオーバーライドして使用する
    function setHeader(){
        header('Content-Type: text/html; charset=utf-8 ;Content-Language: ja');
        set_no_cache();
    }
    
    // セッションチェックを行う。falseが返ったら403 Forbiddenをクライアントに返す。
    // Session Beanの持ち方がプロジェクトごとに違うと思うので、プロジェクトのベースコントローラでセッションの前提
    // 条件を記載しておくと楽。
    function sessionCheck() {
        return true;
    }
    
    //ここがメインロジック。各コントローラで実装する。内部で必ずsetViewを呼び出してViewをセットする必要がある。
    //↑この設計はちょっと疑問。RedirectやファイルDLのハンドリングをどうするか？
    abstract function run();
    
    function setView($filename, $tpl_array = array()){
        //$stashにイテレータを入れたりした場合など、一括処理が上手く行かないが、
        //$stashへのオブジェクトのセットは無しの方向で。やる場合は自己責任で。
        $tpl_array = Yapafi_Controller::_deep_htmlspecialchars( $tpl_array ); //一括エスケープ
        if ( mb_internal_encoding() != OUTPUT_ENCODING ){ //一括エンコーディング
            $tpl_array = Yapafi_Controller::_deep_mb_convert_encoding( 
                $tpl_array, OUTPUT_ENCODING, mb_internal_encoding()
            );
        }
        $this->stash = array_merge( $this->stash, $tpl_array );
        $this->view_filename = $filename;
    }
    
    final function getView(){
        if( !$this->view_filename ){//ビューが指定されていなかったら規定のビューを返す
            $view = get_class($this);
            $view = preg_replace('/_c$/','',$view);
            $view = preg_replace('/_/','/',$view);
            $view = ltrim(preg_replace('/([A-Z])/e',"'_'.strtolower('$1')",$view),'_');
            $view = preg_replace('/^_/','',$view);
            return $view . '.tpl';
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

function render($filename, $stash = array()){
    ob_start();
    require $filename;
    $str = ob_get_contents();
    ob_end_clean();
    return $str;
}

function h($str){
    return htmlspecialchars($str, ENT_QUOTES);
}

function d($obj){
    ob_start();
    var_dump($obj);
    $str = ob_get_contents();
    ob_end_clean();
    return $str;
}

function logging($str, $level = 'DEBUG'){
    $loglevel = array(
        'DEBUG' => 1,
        'INFO'  => 2,
        'WARN'  => 3,
        'ERROR' => 4,
        'FATAL' => 5,
    );
    if ( !array_key_exists( $level, $loglevel ) ){
        logging( 'Log level ['. $level .'] not found.', 'WARN');
    }
    elseif ( $loglevel[YAPAFI_LOG_LEVEL] > $loglevel[$level] ){
        return;
    }
    $traces = debug_backtrace();
    $trace  = $traces[0];
    $now = getDate();
    file_put_contents(
        YAPAFI_LOG_PATH . 'app'.date('Ymd', $now[0]).'.log',
        '['. date('H:i:s', $now[0]).'] '.$level. ' - '. $str . ' at ' .$trace['file'].' line '. $trace['line'] . "\n",
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
    if ( !preg_match( '!^https?://!', $url ) ){
        $url = get_absolute_url( current_url(), $url );
    }
    header('HTTP/1.1 '.$response_code.' '.$msgs[$response_code]);
    header("Location: $url");exit();
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
    header('Content-Length: '. strlen($data));
    
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


//URL系関数群
// MEMO query_stringにスラッシュが入っても相対パスは狂わない
// base_dir 辺りも実装?

function get_absolute_url( $base_url, $relative_path ){
    // query_stringがあったら削除(query_stringにスラッシュが含まれる可能性があるため)
    $base_url = preg_replace('/\?.*$/', '', $base_url);
    // 最後のスラッシュ以降を切り捨て
    $base_url = preg_replace('!/[^/]*$!', '/', $base_url );
    
    preg_match( '!^(https?:)(//[^/]+/)(.*)$!', $base_url, $matches );
    $scheme   = $matches[1];
    $hostname = $matches[2];
    $path     = $matches[3];
    
    if ( preg_match('!^/!', $relative_path) ){
        if ( preg_match('!^//[^/]!', $relative_path ) ){
            return $scheme . $relative_path;
        }
        else{
            $relative_path = preg_replace('!^/!','',$relative_path);
            return $scheme.$hostname.$relative_path;
        }
    }
    
    $relative_path = preg_replace('!^\./!', '', $relative_path);
    $absolute_path = $path . $relative_path;
    
    while ( preg_match('![^/]*/\.\./!', $absolute_path) ){
        $absolute_path = preg_replace('![^/]*/\.\./!', '', $absolute_path);
    }
    while ( preg_match('!^\.\./!', $absolute_path) ){
        $absolute_path = preg_replace('!^\.\./!', '', $absolute_path);
    }
    while ( preg_match('!^\./!', $absolute_path) ){
        $absolute_path = preg_replace('!^\./!', '', $absolute_path);
    }
    while ( preg_match('!/\./!', $absolute_path) ){
        $absolute_path = preg_replace('!/\./!', '/', $absolute_path);
    }
    return $scheme . $hostname . $absolute_path;
}

function uri_for($path, $query_hash){
    // TODO
}

function _scheme(){
    if ( defined('YAPAFI_SCHEME') ){
        return YAPAFI_SCHEME;
    }
    elseif ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == '443'  ){
        return 'https';
    }
    else{
        return 'http';
    }
}

function hostname(){
    if ( defined('YAPAFI_HOSTNAME') ){
        return YAPAFI_HOSTNAME;
    }
    elseif ( isset($_SERVER['HTTP_HOST']) ){
        return $_SERVER['HTTP_HOST'];
    }
    elseif ( isset($_SERVER['SERVER_NAME']) ){
        return $_SERVER['SERVER_NAME'];
    }
    else {
        return $_SERVER['SERVER_ADDR'];
    }
}

function _port_str(){
    if( $_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443' ){
        return ':'.$_SERVER['SERVER_PORT'];
    }
    return '';
}

function current_url(){
    return rooturl() . $_SERVER['REQUEST_URI'];
}

function rooturl(){
    return _scheme() . '://' . hostname() . _port_str();
}

function approot(){
    $approot = $_SERVER['SCRIPT_NAME'];
    //最後のスラッシュ以降を切り捨てて返す
    return preg_replace('!/[^/]+$!', '/', $approot); 
}



?>
