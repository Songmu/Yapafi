<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2>自動エスケープ</h2>
<p>テンプレートに差し込んだ文字列は($stashを使っている限り)自動的にエスケープされます。</p>
<?= $stash['auto_escape'] ?>    
<p>自動エスケープの対象にしたくない場合は、$stashにセットする際にraw_str関数でラッピングしておきます。</p>
<pre><code>$this->stash['no_escape'] = raw_str('<?= $stash['auto_escape'] ?>');</code></pre>
<?= $stash['no_escape'] ?>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
