<?php
class DataValidator_Web extends DataValidator_Base {
    
    protected $error_messages = array(
        'EMAIL'       => 'メールアドレスの形式が不正です',
        'URL'         => 'URLをご確認下さい',
        'EMAIL_LOOSE' => 'メールアドレスの形式が不正です',
    );
    
    
    // http://example.com のようにホスト名の後にスラッシュがなくてもtrueであるようにしています。
    
    function checkURL($val){
        return (bool)preg_match(
            '/\A
                (?:https?):\/\/                                                          # sheme ( reject ftp shttp )
                (?:(?:[-_.!~*\'()a-zA-Z0-9;:&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*@)?          # user & password (optional)
                (?:
                    (?:(?:[a-zA-Z0-9]|[a-zA-Z0-9][-a-zA-Z0-9]*[a-zA-Z0-9])\.)*           # host address
                    (?:[a-zA-Z]|[a-zA-Z][-a-zA-Z0-9]*[a-zA-Z0-9])\.?
                    |
                    [0-9]+\.[0-9]+\.[0-9]+\.[0-9]+                                       # or IPv4 address
                )
                (?::[0-9]*)?                                                             # port (optional)
                (?:
                    (?:                                                                  # path (optional)
                        \/(?:[-_.!~*\'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*
                        (?:;                                                             # path segment could separated by semicolon
                            (?:[-_.!~*\'()a-zA-Z0-9:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*
                        )*
                    )+
                    (?:\?(?:[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*)? # query string (optional)
                    (?:#(?:[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,]|%[0-9A-Fa-f][0-9A-Fa-f])*)?  # fragment (optional)
                )?
            \z/xms'
            ,$val
        );
    }
    
    function checkEMAIL($val){
        return (bool)preg_match(
            '@\A
                (?:
                    (?:
                        (?:
                            (?:[-a-zA-Z0-9_!#\$%&\'*+/=?\^`{}~|]+)
                            (?:\.(?:[-a-zA-Z0-9_!#\$%&\'*+/=?\^`{}~|]+))*
                        )
                        |
                        (?:"(?:\\[^\r\n]|[^\\"])*")                         # quoted string
                    )
                )
                \@
                (?:
                    (?:
                        (?:[a-zA-Z0-9_!#\$%&\'*+/=?\^`{}~|\-]+)
                        (?:\.(?:[-a-zA-Z0-9_!#\$%&\'*+/=?\^`{}~|]+))*
                    )
                )
            \z@xms'
            , $val
        );
    }
    
    /* docomo kddi等が発行しているメールアドレスで。ドットの連続や、@マーク直前のドットを許容してしまっているものがあるため、制約をゆるくしたもの
       注意すべきは以下のドメイン
       dion.ne.jp auone-net.jp docomo.ne.jp ezweb.ne.jp
    */
    function checkEMAIL_LOOSE($val){
        return (bool)preg_match(
            '@\A
            (?:
                (?:
                    (?:[-a-zA-Z0-9_!#\$%&\'*+/=?\^`{}~|.]+)
                    |
                    (?:"(?:\\[^\r\n]|[^\\"])*")
                )
            )
            \@
            (?:
                (?:
                    (?:[a-zA-Z0-9_!#\$%&\'*+/=?\^`{}~|\-]+)
                    (?:\.(?:[-a-zA-Z0-9_!#\$%&\'*+/=?\^`{}~|]+))*
                )
            )
            \z@xms'
        , $val);
    }
    
}

