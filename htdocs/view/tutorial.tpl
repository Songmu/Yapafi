<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2><?= $stash['title'] ?></h2>
<h3>ディレクトリ構成</h3>
  <dl class="compact">
  <dt>yapafi.php</dt>
  <dd>フレームワーク本体です。ディスパッチャもかねています</dd>
  <dt>.htaccess</dt>
  <dd>mod_rewriteのルール等が設定されています。</dd>
  <dt>yapafi.ini</dt>
  <dd>Yapafiを動かす上で必要な設定値を記述します。拡張子はiniですが、記述ルールはphpと同じです。</dd>
  <dt>app.ini</dt>
  <dd>アプリケーション固有の設定(DB接続情報等)を記述します。こちらも同様に記述ルールはPHPと同じです。</dd>
  <dt>app/</dt>
  <dd>ここにコントローラを配置します。</dd>
  <dt>view/</dt>
  <dd>ここにビューを配置します。</dd>
  <dt>lib/</dt>
  <dd>ここにライブラリを配置します。</dd>
  <dt>model/</dt>
  <dd>ここにモデルを配置します。</dd>
  <dt>work/</dt>
  <dd>ここはログやセッション情報が格納されます。</dd>
  </dl>
<h3>インストール</h3>
<p>ダウンロードしてきたファイルのhtdocs以下一式をアプリケーション配置ディレクトリにコピーしてください。</p>
<h3>.htaccessの設定</h3>
<p>以下の行をアプリケーションの設置パスに応じて書き換えてください。</p>
<pre><code>RewriteBase /~yapafi/</code></pre>
<p>これでアプリケーションにアクセスして画面が表示されれば設置は完了です。</p>

<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
