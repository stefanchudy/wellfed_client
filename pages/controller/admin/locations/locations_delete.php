<?php

class Controller extends System\MainController
{

    /**
     *
     * @var model_locations $model_locations
     */
    protected $model_locations = Null;

    public function init()
    {
        $this->loadModel('locations');

        if (isset($this->input->get['id'])) {
            $id = (int)$this->input->get['id'];
            $this->model_locations->delete($id);
        }
        $this->redirect('admin/locations');


    }

}
