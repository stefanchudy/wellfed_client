<?php

class Controller extends System\MainController
{

    public function init()
    {
        if (!isset($this->input->get['id'])) {
            $this->redirect('admin/dashboard');
        }
        $id = $this->input->get['id'];

        $this->user->deleteUser($id);

        $this->redirect('admin/users');
    }

}
