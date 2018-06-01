<?php

class Controller extends System\MainController
{
    /**
     *
     * @var model_blog
     */
    protected $model_blog = Null;

    public function init()
    {
        $this->pageData[API_URL] = $this->db_settings->get(API_URL) . '/';

        $this->loadModel('blog');
        $data = $this->model_blog
            ->setCallMode(model_blog::CALL_MODE_FRONT)
            ->setFilter( isset($this->input->get['month']) ? $this->input->get['month'] : null)
            ->getCollection();

        $this->pageData['collection'] = $data['collection'];

        $this->pageData['sidebar'] = $this->renderWidget('widget/blog_sidebar', $data);

        $this->html->setTitle($this->getSiteName() . ' | Blog');
        $this->renderPage('front/blog/index');
    }

}
