<? $stash['title']='コントローラーが無い場合' ?>
<? require 'sub/htmlhead.tmpl' ?>
<? require 'sub/header.tmpl' ?>
<h2>コントローラーが無い場合</h2>
<p>コントローラーを置かなくても、アプリケーションルート直下にテンプレート(.tmplファイル)を配置すれば、そちらが直接呼び出され実行されます。</p>
<p>あまり推奨されない機能ですが、モックアップを作りながら平行してアプリケーションも作成する際などに使うと良いでしょう。</p>
<? require 'sub/footer.tmpl' ?>
<? require 'sub/footjs.tmpl' ?>