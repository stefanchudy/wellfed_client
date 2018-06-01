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
        if (isset($this->input->get['id'])) {
            $id = (int)$this->input->get['id'];
            $this->loadModel('blog')
                ->deleteRecord($id);
        }
        $this->redirect('admin/blog');
    }

}
