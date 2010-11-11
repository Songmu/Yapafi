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

// 別ファイルからincludeされた場合は、ディスパッチャを実行しない
// realpath()は相対パスや、Windowsのバックスラッシュ対策
if ( realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__) ){
    // ディスパッチャ起動
    try{
        if ( preg_match('/yapafi\.php/i', $_SERVER['REQUEST_URI'] ) ){ // basename(__FILE__) を使う？
            // yapafi.php/pathinfo みたいなURLにアクセスがあった場合に弾く
            header("HTTP/1.1 404 Not Found");
            not_found();exit;
        }
        
        //PATH_INFOからコントローラ名を取得する
        $cntl_name = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/index';
        $cntl_name = strtolower($cntl_name); // 全部小文字に(URLは基本的に小文字のみの前提。というかケースインセンシティヴ)

        $args = array(); //URL引数を使う場合の引数を格納する
        if ( preg_match( '!/$!', $cntl_name ) ){ //スラッシュで終わっている場合、URL引数に空文字列を入れる。
            $cntl_name = preg_replace( '!/$!', '', $cntl_name );
            $args[] = '';
        }

        // $cntl_nameが正しい(制約に沿っている)かどうかのチェック。
        if (!preg_match('!
            ^                   #行頭
            (?:
                /[a-z]          # スラッシュ、半角英字で始まり
                [a-z0-9]*       # その後半角英数字が0文字以上続く
                (?:
                    _[a-z]          # アンスコ、半角英字で始まり
                    [a-z0-9]*       # その後半角英数字が0文字以上続く
                )*                  # このグループが0個以上連続する
            )+                  # このグループが1個以上連続する
            # \.[a-z0-9]+       # （こんな感じで拡張子を許容することも検討中）
            $                   # 行末
            !x', $cntl_name)
        ){
            header("HTTP/1.1 404 Not Found");
            not_found();exit;
        }
        
        $cntl_name = preg_replace('!^/!','',$cntl_name); // 頭のスラッシュを削除

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
            $response_body = $obj->run();
            if ( is_null( $response_body ) ){
                //$obj->render とかの方が良いか？
                $response_body = render($obj->getView(), $obj->stash );
            }
            ob_start("ob_gzhandler");
            $obj->setHeader();
            
            echo $response_body;
            ob_end_flush(); flush();
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
            // ちょっとこの元に戻す処理のやっつけ感が凄い。なんとかしないと。
            // あとファイル名の先頭にアンスコがあるパターンとかアンスコが続くパターンとか無理。
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
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    session_destroy();
}

// ちょっと引数持ち過ぎかなぁ…
// サイズの大きなファイルの場合のバッファ制御とか考慮に入れてないけど、そういう場合は自分で頑張って下さい。
function download_file( $file, $dl_file_name = '', $mime_type = 'application/octet-stream', $charset = '', $delete_after = false ){
    if ( !$dl_file_name ) { $dl_file_name = basename($file); }
    set_dl_header( $dl_file_name, $mime_type, $charset);
    header('Content-Length: '. filesize($file));
    
    readfile( $file );
    if ( $delete_after ) { unlink($file); } exit;
}

function download_data( $data, $file_name, $mime_type = 'text/plain', $charset = '' ){
    set_dl_header( $file_name, $mime_type, $charset);
    header('Content-Length: '. strlen($data));
    
    echo $data;exit;
}

function set_dl_header($file_name, $mime_type, $charset){
    set_no_cache();
    $content_type = "$mime_type";
    if ( $charset ){
        $content_type .= '; charset='. $charset;
    }
    header('Content-Type: '.$content_type);
    header('Content-Disposition: attachment; filename="'.$file_name.'"');
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
    $url_info = parse_url($base_url);
    if ( !$url_info ){
        throw new Excetion('URL parsing failed in function: get_absolute_url().');
    }
    $scheme   = $url_info['scheme'] . ':';
    $hostname = '//'.$url_info['host'];
    if ( isset( $url_info['port'] ) ){
        $hostname .= ':'.$url_info['port'];
    }
    $path = preg_replace('![^/]+$!','',$url_info['path']);
    
    if ( preg_match('!^/!', $relative_path) ){ 
        // $relative_pathが'//'から始まる場合(レアケース)
        if ( preg_match('!^//[^/]!', $relative_path ) ){
            return $scheme . $relative_path;
        }
        else{ // ROOTからの絶対パスの場合
            $relative_path = preg_replace('!^/!','',$relative_path);
            return $scheme.$hostname.$relative_path;
        }
    }
    $absolute_path = $path . $relative_path;
    while ( preg_match('!/\./!', $absolute_path) ){ //なんかこの書き方効率悪い…。PHPでベターな書き方は？
        $absolute_path = preg_replace('!/\./!', '/', $absolute_path);
    }
    while ( preg_match('!/[^/]*/\.\./!', $absolute_path) ){
        $absolute_path = preg_replace('!/[^/]*/\.\./!', '/', $absolute_path);
    }
    while ( preg_match('!^/\.\./!', $absolute_path) ){
        $absolute_path = preg_replace('!^/\.\./!', '/', $absolute_path);
    }
    return $scheme . $hostname . $absolute_path;
}

function uri_for($path, $query_hash){
    return $path . '?'. http_build_query($query_hash);
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


