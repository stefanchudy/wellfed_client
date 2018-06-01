<?php

class Controller extends System\MainController {

    public function init() {
        $action = 'add';

        $this->setValidation();

        $this->pageData['action'] = $action;
        $this->pageData['allergy'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        if (isset($this->input->post['email'])) {

            $this->errors = $this->validator->validateAll($this->input->post);

            $this->pageData['email'] = $this->input->post['email'];
            $this->pageData['pass1'] = $this->input->post['pass1'];
            $this->pageData['pass2'] = $this->input->post['pass2'];
            $this->pageData['first_name'] = $this->input->post['first_name'];
            $this->pageData['last_name'] = $this->input->post['last_name'];
            $this->pageData['mobile_phone'] = $this->input->post['mobile_phone'];
            $this->pageData['allergy'] = $this->input->post['allergy'];
            $this->pageData['preferences'] = $this->input->post['preferences'];


            if (count($this->errors) == 0) {
                $email = $this->pageData['email'];
                $pass = $this->pageData['pass1'];
                $screen_name = $this->pageData['first_name'] . ' ' . $this->pageData['last_name'];
                $access = str_repeat('0', 50);
                $alergies = implode('', $this->pageData['allergy']);

                $register = $this->user->register($email, $pass, $screen_name, $access);


                $register = $this->user->add(
                    array(
                        'email' => $this->pageData['email'],
                        'password' => $this->pageData['pass1'],
                        'first_name' => $this->db->escape($this->pageData['first_name']),
                        'last_name' => $this->db->escape($this->pageData['last_name']),
                        'mobile_phone' => $this->db->escape($this->pageData['mobile_phone']),
                        'preferences' => (int) $this->pageData['preferences'],
                        'alergies' => '@"' . $alergies . '"',
                    )
                );
                if(empty($register['errors'])){
                    $newUserId = $register['result']['id'];
                    $this->redirect('admin/users/edit?id='.$newUserId);
                } else {
                    $this->errors = array_merge($this->errors,$register['errors']);
                }


            }
        }

        $this->html->setTitle($this->getSiteShortName() . ' administration | Add new user');

        $this->renderPage('admin/users/user_details');
    }

    private function setValidation() {

        $this->validator->addValidation('email', Utility\Validator::PATTERN_REQUIRED, NULL, 'The E-mail field is required!');
        $this->validator->addValidation('email', Utility\Validator::PATTERN_EMAIL);
        $this->validator->addValidation('email', Utility\Validator::PATTERN_MAX_VALUE, 5);
        $this->validator->addValidation('email', Utility\Validator::PATTERN_CUSTOM_FUNCTION, function($email) {
            return $this->user->exists($email);
        }, 'This e-mail allready exists in the database!');

        $this->validator->addValidation('pass1', Utility\Validator::PATTERN_MIN_LENGTH, 6, 'The password cannot be shorter than 6 characters.');
        $this->validator->addValidation('pass1', Utility\Validator::PATTERN_MAX_LENGTH, 20, 'The password cannot be longer than 20 characters.');
        $this->validator->addValidation('pass1', Utility\Validator::PATTERN_REQUIRED, NULL, 'You must provide password');
        $this->validator->addValidation('pass2', Utility\Validator::PATTERN_CUSTOM_FUNCTION, function() {
            return ($this->input->post['pass1'] == $this->input->post['pass2']);
        }, 'The two passwords does not match');

        $this->validator->addValidation('first_name', Utility\Validator::PATTERN_MUST_NOT_START_WITH_NUMBER);
        $this->validator->addValidation('first_name', Utility\Validator::PATTERN_FORBIDDEN);
        $this->validator->addValidation('first_name', Utility\Validator::PATTERN_MAX_LENGTH, 50);

        $this->validator->addValidation('last_name', Utility\Validator::PATTERN_MUST_NOT_START_WITH_NUMBER);
        $this->validator->addValidation('last_name', Utility\Validator::PATTERN_FORBIDDEN);
        $this->validator->addValidation('last_name', Utility\Validator::PATTERN_MAX_LENGTH, 50);

        $this->validator->addValidation('mobile_phone', Utility\Validator::PATTERN_MAX_LENGTH, 20);
        $this->validator->addValidation('mobile_phone', Utility\Validator::PATTERN_FORBIDDEN);
    }

}
