<?php
class Controller extends System\MainController {

    /**
     *
     * @var model_arearestrictions
     */
    protected $model_arearestrictions = Null;

    public function init() {

        $this->pageData['api_url'] = $this->db_settings->get(API_URL).'/';

        $this->loadModel('arearestrictions');

        $this->pageData['slides'] = $this->model_arearestrictions->getCollection();

        $this->html->setTitle($this->getSiteShortName() . ' | Working areas');
        
        $this->renderPage('admin/slider/slider');
        
    }

}
