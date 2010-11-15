<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2><?= $stash['title'] ?></h2>

<pre><? var_dump( debug_backtrace()); ?></pre>

<? 
    ld()
?>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
