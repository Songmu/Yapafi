<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2>拡張子を持たせるパターン</h2>
<p>拡張子を持たせることが出来ます。以下のような場合の用途を想定しています。</p>
<ul>
    <li>API用途でモデルから取得したデータ構造を拡張子に応じてjsonやxml、yaml等で出し分けを行いたい場合</li>
    <li>拡張子が.htmlになっているほうがSEO的に有利(笑)</li>
</ul>
<p>コントローラクラスの$allow_exts配列にそのクラスが持ちうる拡張子を記述出来ます。</p>
<p>また、定数:YAPAFI_DEFAULT_EXTを指定することでサイトのデフォルト拡張子を指定できます。</p>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
