<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2>Yapafiとは</h2>
<p>PHPの軽量１ファイルフレームワークです。以下の実現を目指しています。</p>


<ul>
<li>MVCに即した開発</li>
<li>クールなURLとREST対応</li>
<li>PHPの特性を損なうことなく、なるべく生PHPに近い記述ルール
    <ul>
    <li>学習コストが低い</li>
    <li>テンプレートは生PHP</li>
    <li>必要以上にオブジェクトを作らず関数で何とかする</li>
    </ul>
</li>
</ul>

<h2>TODO</h2>
<pre><? readfile('../TODO') ?></pre>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
