<?php
// Yapafi - Yet Another PHP Application Frawework Interface
// Author:  Masayuki Matsuki
// Version: 0.01
// パス情報やファイル情報など即値が多いので、余裕があれば見直したい。(規約と言い切るという手もあるが…)
set_include_path(get_include_path().PATH_SEPARATOR.'view/');
include_once "yapafi.ini"; // session_error(), not_found()を定義
error_reporting(YAPAFI_ERROR_LEVEL);
// undefined function や parse errorを補足できない…。
set_error_handler('_exception_error_handler', YAPAFI_ERROR_LEVEL);
// これで undefined function は捕捉可能になったが、parse error, redeclareは無理。まあ仕方が無いか。
register_shutdown_function('_shutdown_handler');
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
        $cntl_name = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        if ( !$cntl_name ){//CGI版PHP対策(mod_rewriteでPATH_INFOを付与すると上手くいかない)
            $cntl_name = _get_path_info();
        }
        if ( $cntl_name == '/' ){
            $cntl_name = '/index';
        }
        $cntl_name = strtolower($cntl_name); // 全部小文字に(URLは基本的に小文字のみの前提。というかケースインセンシティヴ)

        $args = array(); //URL引数を使う場合の引数を格納する
        if ( preg_match( '!/$!', $cntl_name ) ){ //スラッシュで終わっている場合、URL引数に空文字列を入れる。
            $cntl_name = preg_replace( '!/$!', '', $cntl_name );
            $args[] = '';
        }

        // $cntl_nameが正しい(制約に沿っている)かどうかのチェック。
        if (!preg_match('!
            ^                   #行頭
            ((?:
                /[a-z]          # スラッシュ、半角英字で始まり
                [a-z0-9]*       # その後半角英数字が0文字以上続く
                (?:
                    _[a-z]          # アンスコ、半角英字で始まり
                    [a-z0-9]*       # その後半角英数字が0文字以上続く
                )*                  # このグループが0個以上連続する
            )+)                  # このグループが1個以上連続する
            ((?:/[a-z0-9_]+)*)   # URL引数部分(Optional)
            ((?:\.([a-z0-9]+))?) # 拡張子
            $                    # 行末
            !x', $cntl_name, $matches)
        ){
            header("HTTP/1.1 404 Not Found");
            not_found();exit;
        }
        
        $cntl_name = $matches[1];
        if ( $matches[2] !== '' ){
            $matches[2] = preg_replace( '!^/!', '', $matches[2] );
            $args = array_merge(explode('/', $matches[2]), $args);
        }
        $ext = preg_replace('!^\\.!', '', $matches[3]);  // サイトのデフォルト拡張子とかを決める？
        $cntl_name = preg_replace('!^/!','',$cntl_name); // 頭のスラッシュを削除

        //コントローラファイルが無い場合
        if(  !file_exists( 'app/'.$cntl_name.'.php' ) ){
            if ( file_exists( $cntl_name.'.tpl' )  ){
                // '/'(アプリケーションルートディレクトリ)以下にビューがある場合はビューを直接呼び出す。
                require_once $cntl_name.'.tpl'; exit;
            }
            else { //上にさかのぼってコントローラがあるかどうか探索する
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

        // PATH_INFOのPascalize(camelize)を行い、スラッシュをアンスコに変換、最後に'_c'を加えたのがクラス名になる。
        // 別に通常コントローラ1つしかロードしないのでクラス名を別にする必要性は少ないが、継承させる場合なんかを考えて別にする。
        // (最後に'_c'を付けるのは別のモジュールとクラス名のバッティングを防ぐため。名前空間が使えると良いんですけどね…。)
        $cntl_name = str_replace(array('_',' '), array('',''), ucwords(str_replace(array('/','_'), array('/ ','_ '), $cntl_name))); //Pascalize
        $cntl_name = preg_replace('!/!','_',$cntl_name) . '_c';
        try {
            $obj = new $cntl_name($args, $ext);
        } // コンストラクタで引数チェックにミスると例外が投げられる(ちょっと乱暴か？)
        catch ( YapafiException $ex ) {
            header("HTTP/1.1 404 Not Found");
            not_found();exit;
        }
        $obj->init();
        
        if( $obj->sessionCheck() ){
            $method_name = 'run_' . strtolower($_SERVER["REQUEST_METHOD"]);
            $response_body = $obj->$method_name();
            if ( is_null( $response_body ) ){
                //$obj->render とかの方が良いか？
                $response_body = render($obj->getView(), $obj->stash );
            }
            ob_start("ob_gzhandler"); // gzip圧縮転送の開始
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
        $output = ob_get_contents(); //バッファになんか溜まっている可能性があるので変数に格納して削除
        ob_end_clean();
        header("HTTP/1.1 500 Internal Server Error");
        if ( YAPAFI_DEBUG ){
            require 'extlib/Devel/BackTraceAsHTML.php';
            echo Devel_BackTraceAsHTML::render($ex);
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
   このクラスを直接継承するよりも、アプリケーション毎にベースのクラスを作り、
   それを各コントローラーで継承するのがベタープラクティス
*/
abstract class Yapafi_Controller{
    private $view_filename;
    public  $stash = array();
    public  $ext = '';
    
    protected $has_args = 0;
    protected $allow_exts = array(YAPAFI_DEFAULT_EXT,);
    protected $args;
    
    function __construct( $args, $ext ){
        if ( count($args) > $this->has_args ){
            throw new YapafiException('Args too many!');
        }
        if ( !in_array($ext, $this->allow_exts) ){
            throw new YapafiException('Not allowed extension!');
        }
        $this->ext  = $ext;
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
    // Session Beanの持ち方がアプリケーションごとに違うと思うので、
    // 継承したベースコントローラでセッションの前提条件を記載しておくと楽。
    function sessionCheck() {
        return true;
    }
    
    //ここがメインロジック。各コントローラで実装する。基本的には内部で必ずsetViewを呼び出してViewをセットする必要がある。
    function run(){}
    
    // HTTP method毎にメソッドを分ける場合に使用。__callでやるのも良いと思うが、パフォーマンスが気になるので
    function run_get(){ return $this->run(); }
    function run_post(){ return $this->run(); }
    function run_put(){ return $this->run(); }
    function run_delete(){ return $this->run(); }
    function run_head(){ return $this->run(); }
    
    //Viewに変数をassignすると共に、変数の一括HTMLエスケープ処理、文字コードエンコーディングを行う。
    //$stashにイテレータを入れたりした場合など、一括処理が上手く行かないが、
    //$stashへのオブジェクトのセットは無しの方向で。やる場合は自己責任で。
    function setView($filename, $tpl_array = array()){
        $tpl_array = Yapafi_Controller::_deep_htmlspecialchars( $tpl_array ); //一括エスケープ
        if ( mb_internal_encoding() != OUTPUT_ENCODING ){ //一括エンコーディング
            $tpl_array = Yapafi_Controller::_deep_mb_convert_encoding( 
                $tpl_array, OUTPUT_ENCODING, mb_internal_encoding()
            );
        }
        $this->stash = array_merge( $this->stash, $tpl_array );
        if ( $filename ){
            $this->view_filename = $filename;
        }
    }
    
    final function getView(){
        if( !$this->view_filename ){//ビューが指定されていなかったら規定のビューを返す
            // この元に戻す処理をシンプルに書けないものかね。
            $view = get_class($this);
            $view = preg_replace('/_c$/','',$view);
            $view = str_replace('_','/',$view);
            $view = ltrim(preg_replace('/([A-Z])/e',"'_'.strtolower('$1')",$view),'_');
            $view = str_replace('/_','/',$view);
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

// うーん…。return404って関数名はどうなの？
function return404(){
    header("HTTP/1.1 404 Not Found");
    not_found();exit;
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
function download_file(
    $file, 
    $dl_file_name = '', 
    $mime_type = 'application/octet-stream', 
    $charset = '', 
    $delete_after = false
){
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

// 自分で順次ダウンロード出力を吐き出したい場合はこれを単体で使うと良いと思う。
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

function _exception_error_handler( $err_no, $errstr, $errfile, $errline, $errcontext ){
    // オブジェクトから未定義のスカラをpropertyとしてアクセスした場合( ex. $this->$emp ($empは未定義) )
    // Cannot access empty propertyのエラーが発生するが、補足できない。
    // error_handerには入る。ただしその場合、$errstrはUndefined variable: emp になっている。
    // つまり、まず、$empの解決に行くが、それがundefinedなのでエラーが発生する。-> error_handlerに入る
    // その後、$this->$emp を見に行くが、そこでCannot access empty propertyが発生 -> 補足出来ない
    // (単なる Cannot Access Empty Property は shutdown_handerが補足できる)
    // Cannot access empty property は fatal error
    // コメントアウトすると_shutdown_handlerに入って上手くいく。(一度error_handerに入るとshutdown_handerに入らない？)
    // 例外を投げてもcatchも出来ないし、shutdown_handlerにも入らない…。(多分のfatal errorに止められるせい)
    // $errcontext でも debug_backtraceでもメンバ変数へのアクセスなのか、スコープ変数へのアクセスなのかを判別する
    // 手段がないので、今のところ手詰まり。
    
    throw new ErrorException($errstr, 0, $err_no, $errfile, $errline);
}

function _shutdown_handler(){
    $error = error_get_last();
    if ( isset($error['type']) && in_array($error['type'], Array(
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_COMPILE_ERROR,
        E_CORE_WARNING,    //この辺りのエラーがどの辺に該当するか不明
        E_COMPILE_WARNING, //この辺りのエラーがどの辺に該当するか不明
    ) ) ){
        try{
            // ログ書き出ししたいけど…。register_shutdown_functionでファイルストリーム開けないっぽい。
            ob_get_clean();
            header("HTTP/1.1 500 Internal Server Error");
            if ( YAPAFI_DEBUG ){
                require_once 'extlib/Devel/BackTraceAsHTML.php';
                echo Devel_BackTraceAsHTML::render(array($error), $error['message']);
            }
            else{
                 internal_server_error();
            }
        }
        catch( Exception $ex ){
            //何もしません。出来ません。
        }
    }
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

// CGI PHP環境(さくら)でmod_rewriteを使ってPATH_INFOを付与すると
// no input file specified. というエラーが出るので REQUEST_URIから強引に
// path_info相当の所を取得するための関数。
// PATH_INFO使わずに一律これで行く手もあるけど…。
function _get_path_info(){
    $path_info = $_SERVER['REQUEST_URI'];
    $approot = preg_quote(approot());
    $path_info = preg_replace("!^$approot!", '', $path_info);
    $path_info = preg_replace('!\?.*$!', '', $path_info);
    return '/'.$path_info;
}



