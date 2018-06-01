<?php

namespace Utility;

use System\DB_settings;

class Connector extends MainUtility
{

    /**
     *
     * @var \System\DB_settings $_config
     */
    private $_config = null;
    /**
     * @var \System\PageInput $_input
     */
    private $_input = null;

    private $_remote = null;
    private $_token = null;

    public function __construct()
    {
        $this->_config = \System\DB_settings::getInstance();
        $this->_input = \System\PageInput::getInstance();

    }

    public function __call($name, $arguments)
    {
        $arguments = isset($arguments[0]) ? $arguments[0] : array();

        return $this->getApiData($name, $arguments);
    }

    private function getApiData($method, array $data = array())
    {
        if ((!$this->_getAccessToken()) && (!$this->_getRemoteUrl())) {
            $this->_redirectToInstall();
        }
        $url = $this->_getRemoteUrl() . '/api/' . $method;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $data = is_array($data) ? $data : array($data);

        $origin = $this->_input->url;

        $request_data = array(
            'token' => $this->_getAccessToken(),
            'params' => $data,
            'origin' => $origin ? $origin : 'index'
        );

        $query = json_encode($request_data);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $response_info = curl_getinfo($ch);
        curl_close($ch);

        $response_body = substr($response, $response_info['header_size']);

        $response_json = json_decode($response_body, TRUE);
        $response_data = null;

        if ($method == 'test_token') {
            return isset($response_json['status']['code']) && ($response_json['status']['code'] != 2);
        }

        if (isset($response_json['status']['code' ]) && ($response_json['status']['code'] == 2)) {
            $this->_redirectToInstall();
        } else {
            $response_data = isset($response_json['data']) ? $response_json['data'] : null;
        }

        return $response_data;
    }

    private function _getRemoteUrl()
    {
        if ($this->_remote === null) {
            $this->_remote = $this->_config->get(API_URL);
        }
        return $this->_remote;
    }

    private function _getAccessToken()
    {
        if ($this->_token === null) {
            $this->_token = $this->_config->get(API_TOKEN);
        }
        return $this->_token;
    }

    private function _redirectToInstall()
    {
        if ($this->_input->url != 'install') {
            $this->redirect('install');
            exit;
        }

    }
}
