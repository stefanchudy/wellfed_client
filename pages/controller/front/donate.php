<?php

class Controller extends System\MainController {

    public function init() {
        $this->html->setTitle($this->site_name.' | Support Us');
        
        $this->renderPage('front/donate');
    }

}
