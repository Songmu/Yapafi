<? $stash['title']='コントローラーが無い場合' ?>
<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2>コントローラーが無い場合</h2>
<p>コントローラーを置かなくても、アプリケーションルート直下にテンプレート(.tplファイル)を配置すれば、そちらが直接呼び出され実行されます。</p>
<p>あまり推奨されない機能ですが、モックアップを作りながら平行してアプリケーションも作成する際などに使うと良いでしょう。</p>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
