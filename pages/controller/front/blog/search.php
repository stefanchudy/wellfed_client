<?php

class Controller extends System\MainController {

    /**
     *
     * @var model_blog
     */
    protected $model_blog = Null;

    /**
     *
     * @var Utility\Connector $_connector 
     */
    protected $_connector = null;

    public function init() {
        $this->loadModel('blog');

        if (isset($this->input->get['search_string'])) {
            $search_string = $this->input->get['search_string'];
            if (!empty($search_string)) {
                $request = $this->model_blog->searchInBlog($search_string);

                $this->pageData['collection'] = $request['collection'];                
                $this->pageData['sidebar'] = $this->renderWidget('widget/blog_sidebar',$request );
            } else {
                $this->redirect('blog');
            }
        } else {
            $this->redirect('blog');
        }

        $this->html->setTitle($this->getSiteName() . ' | Blog | Search');
        $this->renderPage('front/blog/search');
    }

}
