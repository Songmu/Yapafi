<?php
class Sample_Die_c extends Yapafi_Controller {
    // コントローラだけでは分かりませんが、テンプレート内で例外を投げています。
    // YAPAFI_DEBUG が true になっている場合、エラー発生時にデバッグ画面を表示します。
    // リリース時には falseにするように気をつけてください。
    function run() {
        $this->stash = array(
            'title' => 'Test',
        );
    }
}

