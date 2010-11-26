<?php
class Sample_Download_c extends Yapafi_Controller {
    function run() {
        // download_file()関数を準備しています。
        // 第2引数以降で、ダウンロードファイル名等が指定できます。
        // 変数の値等を返したい場合は、download_data()関数を使ってください。
        download_file('../data/kuaiwiki.png');
    }
}

