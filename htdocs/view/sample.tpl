<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2>Sample</h2>
<ul class="samples">
<li><a href="no_cntl">コントローラーの無いパターン</a></li>
<li><a href="sample/redirect">リダイレクト(トップページへリダイレクトします)</a></li>
<li><a href="sample/redirect_out">外部リダイレクト(Googleへリダイレクトします)</a></li>
<li><a href="sample/download">ファイルダウンロード(画像をダウンロードします)</a></li>
<li><a href="sample/sjis">文字コード変換(Shift_JISのページを表示させる場合)</a></li>
<li><a href="sample/uri_args/111">URLを引数に取るパターン</a></li>
<li><a href="sample/ext.html">URLに拡張子を持たせるパターン</a></li>
<li><a href="sample/auto_escape">自動エスケープ</a></li>
<li><a href="sample/api.json">コントローラから直接値を返してAPI的に使うパターン(jsonを返します)</a></li>
<li><a href="sample/bbs">一行掲示板</a></li>
<li><a href="sample/die">エラー画面(開発中用)</a></li>
<li><a href="sample/send_mail">メールの送り方</a></li>
<li><a href="sample/form">フォームの値検証</a></li>
<li><a href="sample/view_image/oauth.png">画像をPHPから表示する</a></li>
<li><a href="sample/file_upload">ファイルアップロード</a></li>
</ul>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
