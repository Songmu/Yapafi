<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2>メールの送り方</h2>

<p>Mail_SendJp モジュールを使うと良いでしょう。簡単なテンプレート機能を持ち、文字コード変換などを適宜行ってくれます。</p>

<pre><code>        require_once 'Mail/SendJp.php'; //Mail_SendJp モジュールを使います。
        $mailer = new Mail_SendJp(); //オブジェクトを作成します。
        // Mail_SendJpでは内部的にはmail()関数を使っています。
        $success = $mailer->send(
            'hoge@example.com, fuga@example.com', // 宛先(カンマ区切りで複数指定可能
            'mailtpl.txt', // メールテンプレート
            array(         // テンプレートへの差込文字列を連想配列で指定
                'subject' => '日本語サブジェクト',
                'var'     => '本文への差込文字列です。',
            )
        );</code></pre>

<p>テンプレートはこんな感じで作成します。PHPの文字列と同じ感じです。例によって、文字コードはUTF-8にしてください。連想配列のキーに応じた変数名の部分が置き換えられます。</p>

<pre>subject: $subject
From: example@example.com
CC: cc@example.com

{$name}様

このたびは{$item}のご購入、まことにありがとうございました。
...</pre>

<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
