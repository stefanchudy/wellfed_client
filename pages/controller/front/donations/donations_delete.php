<?php

class Controller extends System\MainController {

    /**
     *
     * @var model_donations $model_donations
     */
    protected $model_donations = Null;


    public function init() {

        if (isset($this->input->get['id']) && $this->user->logged) {
            $id = (int) $this->input->get['id'];
            $this->loadModel('donations');

            if ($donation = $this->model_donations->getDonation($id)) {
                if ($donation['location_data']['user_id'] == $this->user->logged['id']) {
                    $this->model_donations->delete($id);
                }
            }
        }
        $this->redirect('profile');
    }

}
