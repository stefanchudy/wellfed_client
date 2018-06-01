<?php

class Controller extends System\MainController {

    /**
     *
     * @var model_blog
     */
    protected $model_blog = Null;

    public function init() {
        $this->loadModel('blog');

        $this->pageData[API_URL] = $this->db_settings->get(API_URL) . '/' ;

        if ($id = $this->get_hidden['id']) {
            if ($request = $this->model_blog->getBlogPost($id)) {

                $this->html->setSocialTags(array(
                    'image' => $this->db_settings->get(API_URL) . '/' . $request['post']['image'],
                    'title' => $request['post']['title'],
                    'description' => strip_tags(html_entity_decode($request['post']['content']))
                ));
                
                $this->pageData['sidebar'] = $this->renderWidget('widget/blog_sidebar', $request);

                $this->pageData['postData'] = $request['post'];
            } else {
                $this->redirect('');
            }
        } else {
            $this->redirect('blog');
        }
        $this->html->setTitle($this->getSiteName() . ' | Read post');
        $this->renderPage('front/blog/read');
    }

}
