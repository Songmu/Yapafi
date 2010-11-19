<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2><?= $title ?></h2>
<? foreach ( $column_list as $column ): ?>
    <li><a href="column/<?= $column['link'] ?>.html"><?= $column['title'] ?></a></li>
<? endforeach ?>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
