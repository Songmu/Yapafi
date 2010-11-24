<?php
require_once 'yapafi.php';


echo get_absolute_url('http://www.songmu.jp/hoge/fuga/piyo.html', '../../html.ee');
echo '<br>';

echo get_absolute_url('http://www.songmu.jp/hoge/fuga/piyo.html', '../../ht/./ml.ee');
echo '<br>';

echo get_absolute_url('http://www.songmu.jp/hoge/fuga/piyo.html', './html.ee');
echo '<br>';

echo get_absolute_url('http://www.songmu.jp/hoge/fuga/piyo.html', 'gete/html.ee');
echo '<br>';

echo get_absolute_url('http://www.songmu.jp/hoge/fuga/piyo.html', 'gete/../html.ee');
echo '<br>';


echo $_SERVER['SCRIPT_FILENAME'];
