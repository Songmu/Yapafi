<?php

class Devel_BackTraceAsHTML {
    static function render($trace = null, $msg = ''){
        if ( !$trace ){
            $trace = debug_backtrace();
        }
        
        $out = "<!doctype html><html><head><title>Error: ${msg}</title>";
        $out .= <<<_STYLE_
<style type="text/css">
a.toggle { color: #444 }
body { margin: 0; padding: 0; background: #fff; color: #000; }
h1 { margin: 0 0 .5em; padding: .25em .5em .1em 1.5em; border-bottom: thick solid #002; background: #444; color: #eee; font-size: x-large; }
pre.message { margin: .5em 1em; }
li.frame { font-size: small; margin-top: 3em }
li.frame:nth-child(1) { margin-top: 0 }
pre.context { border: 1px solid #aaa; padding: 0.2em 0; background: #fff; color: #444; font-size: medium; }
pre .match { color: #000;background-color: #f99; font-weight: bold }
pre.vardump { margin:0 }
pre code strong { color: #000; background: #f88; }

table.lexicals, table.arguments { border-collapse: collapse }
table.lexicals td, table.arguments td { border: 1px solid #000; margin: 0; padding: .3em }
table.lexicals tr:nth-child(2n) { background: #DDDDFF }
table.arguments tr:nth-child(2n) { background: #DDFFDD }
.lexicals, .arguments { display: none }
.variable, .value { font-family: monospace; white-space: pre }
td.variable { vertical-align: top }
</style>
_STYLE_;

        $out .= <<<_HEAD_
<script type="text/javascript">
function toggleThing(ref, type, hideMsg, showMsg) {
 var css = document.getElementById(type+'-'+ref).style;
 css.display = css.display == 'block' ? 'none' : 'block';

 var hyperlink = document.getElementById('toggle-'+ref);
 hyperlink.textContent = css.display == 'block' ? hideMsg : showMsg;
}
function toggleArguments(ref) {
 toggleThing(ref, 'arguments', 'Hide function arguments', 'Show function arguments');
}
function toggleLexicals(ref) {
 toggleThing(ref, 'lexicals', 'Hide lexical variables', 'Show lexical variables');
}
</script>
</head>
<body>
<h1>Error trace</h1><pre class="message">$msg</pre><ol>
_HEAD_;
        
        $i = 0;
        foreach ( $trace as $frame ){
            $i++;
            $out .= join(
                '', array(
                '<li class="frame">',
                isset($frame['function']) ? self::_h("in " . $frame['function']) : '',
                ' at ',
                isset($frame['file']) ?self::_h($frame['file']) : '',
                ' line ',
                $frame['line'],
                '<pre class="context"><code>',
                self::_build_context($frame),
                '</code></pre>',
                isset($frame['args']) ? self::_build_arguments($i, $frame['args']) : '',
                '</li>')
            );
        }
        $out .= '</ol>';
        $out .= '</body></html>';
        return $out;
    }
    
    static function _h($str){
        return htmlspecialchars($str, ENT_QUOTES);
    }
    
    static function _build_context( $frame ){
        $file    = $frame['file'];
        $linenum = $frame['line'];
        $code = '';
        if (file_exists($file)) {
            $start = $linenum - 3;
            $end   = $linenum + 3;
            $start = $start < 1 ? 1 : $start;
            
            $fh = fopen($file, 'r'); # or throw
            $cur_line = 0;
            while ( $line = fgets($fh) ) {
                ++$cur_line;
                if ( $cur_line > $end ){ break; }
                if ( $cur_line < $start ){ continue; }
                $line = preg_replace( '!\\t!', '    ', $line);
                $tag = $cur_line == $linenum ? 
                       array('<strong class="match">', '</strong>') :
                       array('','') ;
                $code .= sprintf(
                    '%s%5d: %s%s', $tag[0], $cur_line, self::_h($line),
                    $tag[1]
                );
            }
            fclose($fh);
        }
        return $code;
    }
    
    static function _build_arguments($id, $args) {
        $ref = "arg-$id";
        if ( empty( $args ) ){
            return '';
        }
        $html = <<<_HTML_
<p><a class="toggle" id="toggle-$ref" href="javascript:toggleArguments('$ref')">Show function arguments</a></p><table class="arguments" id="arguments-$ref">
_HTML_;
        $idx = 0;
        foreach ( $args as $value ){
            $html .= '<tr>';
            $html .= '<td class="variable">' . $idx .'</td>';
            $html .= '<td class="value">' . self::_h(self::_d($value)) . '</td>';
            $html .= '</tr>';
            $idx++;
        }
        $html .= '</table>';

        return $html;
    }
    
    static function _d($obj){
        ob_start();
        var_dump($obj);
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }
    
}
