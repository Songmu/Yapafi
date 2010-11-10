<?php

foreach ( $_SERVER as $key => $value ){
    echo $key . ' : ' . $value . "<br>";
}

?>
    <br>
    <?= current_url() ?>
        <br>
        <?= __FILE__ ?>