<?php

class Controller extends System\MainController {

    /**
     *
     * @var model_common
     */
    protected $model_common = Null;

    /**
     *
     * @var model_foods
     */
    protected $model_foods = Null;

     /**
     *
     * @var model_donations $model_donations
     */
    protected $model_donations = Null;

    /**
     * @var model_locations $model_locations
     */
    protected $model_locations = null;

    public function init() {
        date_default_timezone_set('UTC');
        $this->html->addHeaderTag('<script type="text/javascript" src="js/LocationPicker.js"></script>');

        $this->pageData['id'] = 'new';

        $this->loadModel('donations');
        $this->loadModel('locations');
        $this->loadModel('foods');

        $this->pageData['food_types_selector'] = $this->model_foods->getSelector();
        $this->pageData['allergy'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        if (isset($this->input->get['location_id'])) {

            if ($this->model_locations->locationKeyExists($this->input->get['location_id'])) {
                $this->pageData['location_id'] = (int) $this->input->get['location_id'];
            } else {
                $this->redirect('admin/donations/add');
            }
        }

        $this->_setValidation();

        if (count($this->input->post)) {
            $this->errors = $this->validator->validateAll($this->input->post);

            $this->pageData['title'] = $this->input->post['title'];
            $this->pageData['food_types_selector'] = $this->model_foods->getSelector($this->input->post['food_type_id']);
            $this->pageData['expiry_hours'] = (int) $this->input->post['expiry_hours'];
            $this->pageData['quantity'] = (int) $this->input->post['quantity'];
            $this->pageData['location_id'] = (int) $this->input->post['location_id'];
            $this->pageData['description'] = $this->input->post['description'];
            $this->pageData['allergy'] = $this->input->post['allergy'];
            $this->pageData['preferences'] = $this->input->post['preferences'];

            if (count($this->errors) === 0) {
                $date_start = date('Y-m-d H:i:s', time());
                $date_expire = date('Y-m-d H:i:s', strtotime($date_start . ' + ' . $this->input->post['expiry_hours'] . ' hours'));

                $save = Array(
                    'date_created' => '@NOW()',
                    'location_id' => (int) $this->input->post['location_id'],
                    'date_start' => $date_start,
                    'date_expire' => $date_expire,
                    'food_type_id' => (int) $this->input->post['food_type_id'],
                    'title' => $this->db->escape($this->input->post['title']),
                    'description' => $this->db->escape($this->input->post['description']),
                    'allergens' => '@"'.implode('', $this->input->post['allergy']).'"',
                    'quantity' => (int) $this->input->post['quantity'],
                    'preferences' => (int) $this->input->post['preferences'],
                );
                $new_id = $this->model_donations->addDonation($save);
                $this->redirect('admin/donations/edit?id='.$new_id);
            }
        }



        $this->html->setTitle($this->short_name . ' Administration | Add Donations');
        $this->renderPage('admin/donations/donations_details');
    }

    private function _setValidation() {
        $this->validator->addValidation('title', \Utility\Validator::PATTERN_REQUIRED);
        $this->validator->addValidation('title', \Utility\Validator::PATTERN_MIN_LENGTH, 5);
        $this->validator->addValidation('title', \Utility\Validator::PATTERN_MAX_LENGTH, 50);
        $this->validator->addValidation('title', \Utility\Validator::PATTERN_FORBIDDEN);
        $this->validator->addValidation('title', \Utility\Validator::PATTERN_MUST_NOT_START_WITH_NUMBER);

        $this->validator->addValidation('description', \Utility\Validator::PATTERN_FORBIDDEN);

        $this->validator->addValidation('quantity', \Utility\Validator::PATTERN_REQUIRED);
        $this->validator->addValidation('quantity', \Utility\Validator::PATTERN_NUMBER_ONLY);
        $this->validator->addValidation('quantity', \Utility\Validator::PATTERN_MIN_VALUE, 1);
    }

}
