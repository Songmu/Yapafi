<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2>ファイルアップロード</h2>
<p>アップロードした画像ファイルを一覧で表示します</p>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="imgfile" id="imgfile">
    <input type="submit" value="upload!">
</form>
<ul>
<? foreach ( $images as $image ){ ?>
<li><img src="view_image/<?= $image ?>"></li>
<? } ?>
</ul>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
