<? require 'sub/htmlheadsjis.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2><?= $title ?></h2>
<form method="POST" onsubmit="return confirm('メッセージを投稿しますか？');">
<ul>
<li><label for="name">お名前</label>:<input type="text" name="name" id="name" size="10"></li>
<li><label for="comment">コメント</label>:<input type="text" name="comment" id="comment" size="50"></li>
<li class="buttons"><input type="hidden" name="_token" id="_token" value="<?= $_token ?>"><input type="submit" value="投稿"></li>
</ul>
</form>
<? if ( $_msg ){ ?><p class="flush"><?= $_msg ?></p><? } ?>
<dl class="bbs">
<? foreach ( $comments as $comment ) : ?>
<dt><?= $comment['time'] ?>　<?= $comment['name'] ?></dt>
<dd><?= $comment['comment'] ?></dd>
<? endforeach; ?>
</dl>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
