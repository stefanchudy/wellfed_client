<?php

class Controller extends System\MainController {

    /**
     * \Utility\Connector $_connector
     */
    protected $_connector = null;

    /**
     *
     * @var model_arearestrictions
     */
    protected $model_arearestrictions = Null;

    /**
     * @var model_blog $model_blog
     */
    protected $model_blog = null;

    public function init() {
        $this->html->setTitle($this->getSiteName());
        
        $this->loadModel('blog');
        $this->model_blog->setCallMode(model_blog::CALL_MODE_FRONT);
        $this->pageData['blog'] = $this->model_blog->getCollection();

        $this->loadModel('arearestrictions');

        $this->pageData[API_URL] = $this->db_settings->get(API_URL);

        $this->pageData['slider'] = $this->model_arearestrictions->getSlider();

        $this->renderPage('front/index');

    }

}