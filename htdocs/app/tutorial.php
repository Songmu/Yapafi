<?php
require_once 'markdown.php';
class Tutorial_c extends Yapafi_Controller {
    function run() {
        $mkdn_file = 'work/data/tutorial.mkdn';
        $this->stash = array(
            'title' => 'Tutorial',
            'main_contents' => raw_str(Markdown(file_get_contents($mkdn_file))),
        );
    }
}

