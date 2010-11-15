<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2><?= $stash['title'] ?></h2>
<? throw new Exception('こんなふうに死にます。Syntax Error・Cannot releclare辺りのFatal Errorはトラップできません。'); ?>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
