<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2><?= $title ?></h2>
<ul>
<? foreach ( $column_list as $column ): ?>
    <li><a href="column/<?= $column['link'] ?>.html"><?= $column['title'] ?></a></li>
<? endforeach ?>
</ul>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
