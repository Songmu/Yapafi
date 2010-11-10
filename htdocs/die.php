<?php
    
// mentaのエラー表示が進化しているっ！ 
// Show function arguments とか言うのがある！
→ この辺読む
                        require 'Devel/StackTrace.pm';
                        require 'Devel/StackTrace/AsHTML.pm';


Devel::StackTrace::AsHTML

                push @trace,
                    +{
                    level    => $i,
                    package  => $package,
                    filename => $filename,
                    line     => $line,
                    context  => $context,
                }
                
                my ( $package, $filename, $line, ) = caller($i) ) {
                my $context = sub {
                    my ( $file, $linenum ) = @_;
                    my $code;
                    if ( -f $file ) {
                        my $start = $linenum - 3;
                        my $end   = $linenum + 3;
                        $start = $start < 1 ? 1 : $start;
                        open my $fh, '<:utf8', $file or die $file . ': ' . $!;
                        my $cur_line = 0;
                        while ( my $line = <$fh> ) {
                            ++$cur_line;
                            last if $cur_line > $end;
                            next if $cur_line < $start;
                            my @tag
                                = $cur_line == $linenum
                                ? ( q{<strong>}, '</strong>' )
                                : ( '', '' );
                            $code .= sprintf( '%s%5d: %s%s',
                                $tag[0], $cur_line, filter( $line => 'html' ),
                                $tag[1], );
                        }
                        close $file;
                    }
                    return $code;
                    }
                    ->( $filename, $line );


$traces = debug_backtrace();

        $msg = h($ex->get_message);
        $body
            = '<!doctype html><head><title>500 Internal Server Error</title><style type="text/css">body { margin: 0; padding: 0; background: rgb(230, 230, 230); color: rgb(44, 44, 44); } h1 { margin: 0 0 .5em; padding: .25em; border: 0 none; border-bottom: medium solid rgb(0, 0, 15); background: rgb(63, 63, 63); color: rgb(239, 239, 239); font-size: x-large; } p { margin: .5em 1em; } li { font-size: small; } pre { background: rgb(255, 239, 239); color: rgb(47, 47, 47); font-size: medium; } pre code strong { color: rgb(0, 0, 0); background: rgb(255, 143, 143); } p.f { text-align: right; font-size: xx-small; } p.f span { font-size: medium; }</style></head><h1>500 Internal Server Error</h1><p>$msg</p><ol>';
        
        for my $stack ( @{ $err->{trace} } ) {
            $body .= '<li>'
                . filter(
                join( ', ',
                    $stack->{package}, $stack->{filename}, $stack->{line} ) =>
                    'html'
                ) . qq(<pre><code>$stack->{context}</code></pre></li>);
        }
        $body
            .= qq{</ol><p class="f"><span>Powered by <strong>Yapafi</strong></span>, PHP application framework</p>};




