<?php

class Controller extends System\MainController {

    public function init() {
        $this->html->setTitle($this->site_name.' | Partners');
        
        $this->renderPage('front/partners');
    }

}
