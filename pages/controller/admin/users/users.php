<?php

class Controller extends System\MainController {

    /**
     * @var model_users $model_users
     */
    protected $model_users = null;

    public function init() {
        $this->loadModel('users');

        $this->pageData['users'] = $this->model_users->getUsers();
        $this->pageData['user_type'] = 0;

        $this->html->setTitle($this->getSiteShortName() . ' admin | Users management');
        $this->renderPage('admin/users/users');
    }

}
