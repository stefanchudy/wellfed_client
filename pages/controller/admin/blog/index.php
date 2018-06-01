<?php

class Controller extends System\MainController {
    /**
     *
     * @var model_blog
     */
    protected $model_blog = Null;

    public function init() {
        $this->loadModel('blog');

        $this->pageData['posts'] = $this->model_blog->setCallMode(model_blog::CALL_MODE_ADMIN)->getCollection();

        $this->pageData['api_url'] = $this->db_settings->get(API_URL).'/';

        $this->html->setTitle($this->getSiteShortName() . ' Administration | Blog');
        $this->renderPage('admin/blog/index');        
    }

}
