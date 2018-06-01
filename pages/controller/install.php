<?php

class Controller extends System\MainController
{

    private $_connector = null;

    public function init()
    {
        $this->_connector = new Utility\Connector();

        $this->_setValidation();

        if (isset($this->input->post[API_URL], $this->input->post[API_TOKEN])) {
            $this->errors = $this->validator->validateAll($this->input->post);

            if(count($this->errors==0)){
                $this->db_settings->set($this->input->post);
            }
        }

        $this->pageData[API_TOKEN] = $this->db_settings->get(API_TOKEN);
        $this->pageData[API_URL] = $this->db_settings->get(API_URL);

        if($this->_validateToken()){
            $this->redirect('');
        }

        $this->renderPage('install');
    }

    private function _setValidation()
    {
        $this->validator->clear();

        $this->validator->addValidation(API_URL, Utility\Validator::PATTERN_REQUIRED);
        $this->validator->addValidation(API_URL, Utility\Validator::PATTERN_URL);

        $this->validator->addValidation(API_TOKEN, Utility\Validator::PATTERN_REQUIRED);
    }

    private function _validateToken(){
        return $this->_connector->test_token();
    }
}