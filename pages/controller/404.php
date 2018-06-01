<?php

class Controller extends \System\MainController {

    public function init() {
        $this->html->setTitle($this->site_name.' - Error 404');
        $this->renderPage('404');
    }

}
