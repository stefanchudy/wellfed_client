<?php

class Controller extends System\MainController {

    /**
     *
     * @var model_arearestrictions
     */
    protected $model_arearestrictions = Null;

    public function init() {

        if (isset($this->input->get['id'])) {
            $id = (int)$this->input->get['id'];
            $this->loadModel('arearestrictions')
                ->delete($id);

        }

        $this->redirect('admin/working-areas');
    }

}
