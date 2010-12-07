<? require 'sub/htmlhead.tpl' ?>
<? require 'sub/header.tpl' ?>
<h2>フォームの値検証</h2>
<p>FormValidator.phpを使います。</p>
<p>フォームヘルパー、フォームビルダー的な物は用意してません。</p>
<hr>
<? if ( $msg ){ ?>
<p><?= $msg ?></p>
<? } ?>
<? if ( $error_msgs ){ ?>
<ul>
<? foreach ( $error_msgs as $emsg ){ ?>
<li><?= $emsg ?></li>
<? } ?>
</ul>
<? } ?>
<form action="" method="POST">
    <fieldset>
    <dl>
    <dt><label for="name">お名前</label></dt>
    <dd><input type="text" name="name" id="name" value="<?= isset($form_input['name']) ? $form_input['name'] : '' ?>"></dd>
    <dt><label for="mail">メール</label></dt>
    <dd><input type="email" name="mail" id="mail" value="<?= isset($form_input['mail']) ? $form_input['mail'] : '' ?>"></dd>
    <dt><label for="mail2">メール(確認)</label></dt>
    <dd><input type="email" name="mail2" id="mail2" value="<?= isset($form_input['mail2']) ? $form_input['mail2'] : '' ?>"></dd>
    <dt><label for="address">住所</label></dt>
    <dd><input type="text" name="address" id="address" value="<?= isset($form_input['address']) ? $form_input['address'] : '' ?>"></dd>
    <dt><label for="age">年齢</label></dt>
    <dd><input type="text" name="age" id="age" style="ime-mode:disabled" value="<?= isset($form_input['age']) ? $form_input['age'] : '' ?>"></dd>
    <dt><label for="tel">電話番号</label></dt>
    <dd><input type="text" name="tel" id="tel" style="ime-mode:disabled" value="<?= isset($form_input['tel']) ? $form_input['tel'] : '' ?>"></dd>
    <dt><label for="gender">性別</label></dt>
    <dd>
        <? $gender_value = isset($form_input['gender']) ? $form_input['gender'] : ''  ?>
        <label for="female"><input type="radio" name="gender" id="female" value="1"<?= $gender_value == '1' ? ' checked' : ''  ?>>女性</label>
        <label for="male"><input type="radio" name="gender" id="male" value="2"<?= $gender_value == '2' ? 'checked' : ''  ?>>男性</label>
        <label for="other"><input type="radio" name="gender" id="other" value="3"<?= $gender_value == '3' ? 'checked' : ''  ?>>その他</label>
    </dd>
    <dt><label for="secret_num">秘密の番号</label></dt>
    <dd><input type="text" name="secret_num" id="secret_num" style="ime-mode:disabled" value="<?= isset($form_input['secret_num']) ? $form_input['secret_num'] : '' ?>"></dd>
    <dd style="text-align:center"><input type="submit" value="submit!" ></dd>
    
    </fieldset>
</form>
<? require 'sub/footer.tpl' ?>
<? require 'sub/footjs.tpl' ?>
