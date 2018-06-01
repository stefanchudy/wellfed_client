<?php

class Controller extends System\MainController {

    public function init() {
        $this->html->setTitle($this->site_name.' | About us');
        
        $this->renderPage('front/about');
    }

}
