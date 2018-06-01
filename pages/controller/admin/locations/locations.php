<?php

class Controller extends System\MainController {

    /**
     *
     * @var model_locations
     */
    protected $model_locations = Null;

    public function init() {
        $this->loadModel('locations');

        $this->pageData['locations'] = $this->model_locations->getCollection();

        $this->html->setTitle($this->short_name . ' Administration');
        $this->html->render($this->pageData, 'admin/locations/locations');
    }

}
