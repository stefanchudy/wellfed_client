<?php

class Controller extends System\MainController
{

    /**
     *
     * @var model_common
     */
    protected $model_common = Null;

    /**
     *
     * @var model_donations $model_donations
     */
    protected $model_donations = Null;

    public function init()
    {
        date_default_timezone_set('UTC');

        if (isset($this->input->get['id'])) {
            $this->loadModel('donations');

            if ($redirect = $this->model_donations->resetBooking($this->input->get['id'])) {
                $this->redirect('donations/edit?id=' . $redirect . '&tab=1');
            } else {
                $this->redirect('profile');
            }

        } else {
            $this->redirect('profile');
        }


    }

}
