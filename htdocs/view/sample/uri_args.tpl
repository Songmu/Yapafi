<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2>URLを引数として使うパターン</h2>
<p>一つのコントローラでURLを引数に受け取ってハンドリングをすることが出来ます。</p>
<p>ココにURLの末尾の文字列が表示されます。→ <?= $stash['args'][0] ?></p>
<ul>
<? $entries = array('111', '222BCC', '333', '444HHH', 'hoge', 'fuga', 'piyo', 'yapafi1'); 
    foreach ( $entries as $entry ){ ?>
<li><a href="<?= $entry ?>"><?= $entry ?></a></li>
<? } ?>
</ul>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
