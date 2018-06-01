<?php

namespace System\Extend;

class UserExtended extends \System\User
{

    /**
     * \Utility\Validator object
     *
     * @var \Utility\Validator
     */
    private $_validator = NULL;

    /**
     * \Utility\Connector object
     * @var \Utility\Connector $_connector
     */

    /**
     * Limits how many donations can be booked by a single user.
     * If zero - the booking is unlimited.
     * Default value is zero.
     * @var int $_booking_limit
     */
    private $_booking_limit = 0;
    /**
     * Sets how many hours takes for a donation to expire.
     * Default expiry time is 3 hours.
     * @var int $_booking_expiring
     */
    private $_booking_expiring = 3;
    private $_connector = null;

    public function __construct()
    {
        $this->_connector = new \Utility\Connector();
        $this->_validator = new \Utility\Validator();
        $this->config = \System\Config::getInstance();
        $this->db = \System\DB::getInstance();
        $this->input = \System\PageInput::getInstance();
        $this->db_settings = \System\DB_settings::getInstance();

        $this->_booking_expiring = $this->db_settings->get('booking_time', 3);
        $this->_booking_limit = $this->db_settings->get('booking_limit', 0);

        $this->checkSession();
    }

    public function logOff($redirect = TRUE)
    {

        $this->_connector->log_off($this->input->session);


        unset($_SESSION['user_id']);
        unset($_SESSION['session_id']);
        unset($_SESSION['token']);
        $this->logged = false;
        $this->logged = null;
        if ($redirect) {
            header("Location: /");
        }
    }

    public function isAdmin()
    {
        return $this->logged && ($this->logged['is_admin'] === true);
    }

    public function add($params = array())
    {
        $result = array(
            'errors' => [],
            'result' => []
        );
        $this->_validator->clear();
        $this->_setValidationRegister();
        $result['errors'] = $this->_validator->validateAll($params);


        if (count($result['errors']) === 0) {
            $result['result'] = $this->_connector->add_user($params);
        }

        return $result;
    }

    public function login($email, $pass)
    {
        $result = false;
        $query = $this->_connector->login_user(array(
            'email' => $email,
            'pass' => $pass
        ));
        if ($query && (!isset($query['error']))) {
            $result = true;
            $this->createSession($query['id'], $query['email'], $query['password']);
            $this->logged = $result;
            $this->checkSession();
        }
        return $result;
    }

    protected function createSession($user_id, $email, $pass)
    {
        $session_data = $this->_connector->create_session(array(
            'user_id' => $user_id,
            'email' => $email,
            'pass' => $pass,
        ));
        if ($session_data && (!isset($session_data['error']))) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['session_id'] = $session_data['session_id'];
            $_SESSION['token'] = $session_data['token'];
        }

    }

    public function checkSession()
    {
        $check_session = isset(
            $this->input->session['user_id'], $this->input->session['session_id'], $this->input->session['token']
        );
        if ($check_session) {

            $request = $this->_connector->check_session(
                array(
                    'user_id' => $this->input->session['user_id'],
                    'session_id' => $this->input->session['session_id'],
                    'user_token' => $this->input->session['token'],
                )
            );
            if (!isset($request['error'])) {
                $this->logged = $request;
            }

        }
    }

    public function exists($email)
    {
        return $this->_connector->user_exists(['email' => $email]);
    }

    public function uploadImage($user_id = null, $field = 'user_image')
    {
        if ($this->logged && ($user_id === null)) {
            $user_id = $this->logged['id'];
        }

        if ($user_id && isset($_FILES[$field]) && ($_FILES[$field]['error'] == 0)) {
            $upload = $_FILES[$field];
            $user_data = $this->_connector->upload_user_image(
                array(
                    'id' => $user_id,
                    'file_name' => $upload['name'],
                    'file_content' => base64_encode(file_get_contents($upload['tmp_name'])),
                )
            );
            if ($this->logged && ($user_id == $this->logged['id'])) {
                $this->logged = $user_data;
            }
        }
    }

    public function update($id, $mainData = array(), $extraData = array())
    {
        $update = $this->_connector->update_user(
            array(
                'id' => $id,
                'main_data' => $mainData,
                'extra_data' => $extraData
            )
        );
        if (!isset($update['error']) && ($this->logged['id'] == $id)) {

            $this->logged = $update;
        }
    }

    public function upgradeUser($id)
    {
        $this->update($id, Array(), ['advanced' => 1]);
    }

    public function applyUpgrade($id)
    {
        $this->update($id, Array(), ['upgrade_application' => 1]);
    }

    public function rejectUser($id)
    {
        $this->update($id, Array(), array(
            'advanced' => 0,
            'upgrade_application' => 2
        ));
    }

    public function canBookNow()
    {

        if (!$this->logged) {
            return false;
        }
        if ($this->_booking_limit == 0) {
            return true;
        }
        $count_active = 0;
        $expired = date('Y-m-d H:i:s', strtotime('now - ' . $this->_booking_expiring . ' hours'));
        foreach ($this->logged['donations_used'] as $donation) {
            if ($donation['date_booked'] > $expired && $donation['delivered'] == 0)
                $count_active++;

        }
        return ($count_active < $this->_booking_limit);
    }

    public function getUserById($id)
    {
        return $this->_connector->getuser_by_id(
            array(
                'id' => $id
            )
        );
    }

    public function deleteUser($id)
    {
        return $this->_connector->delete_user(
            array(
                'id' => $id
            )
        );
    }

    public function getUserNameById($id = null)
    {
        $user = (($id === null) || ($id == $this->logged['id'])) ? $this->logged : $this->getUserById($id);
        return $user ? ((!empty(trim($user['screen_name']))) ? $user['screen_name'] : ((!empty(trim($user['data']['first_name'] . ' ' . $user['data']['first_name']))) ? $user['data']['first_name'] . ' ' . $user['data']['first_name'] : $user['email'])) : null;
    }

    private function _setValidationRegister()
    {

        $this->_validator->addValidation('email', \Utility\Validator::PATTERN_REQUIRED, NULL, 'The E-mail field is required!');
        $this->_validator->addValidation('email', \Utility\Validator::PATTERN_EMAIL);
        $this->_validator->addValidation('email', \Utility\Validator::PATTERN_MAX_VALUE, 5);
        $this->_validator->addValidation('email', \Utility\Validator::PATTERN_CUSTOM_FUNCTION, function ($param) {
            $result = $this->exists($param);
            if (is_array($result) && isset($result['result'])) {
                return !$result['result'];
            } else {
                return true;
            }
        }, 'This e-mail allready exists in the database!');

        $this->_validator->addValidation('password', \Utility\Validator::PATTERN_MIN_LENGTH, 6, 'The password cannot be shorter than 6 characters.');
        $this->_validator->addValidation('password', \Utility\Validator::PATTERN_MAX_LENGTH, 20, 'The password cannot be longer than 20 characters.');
        $this->_validator->addValidation('password', \Utility\Validator::PATTERN_REQUIRED, NULL, 'You must provide password');
        $this->_validator->addValidation('password', \Utility\Validator::PATTERN_CUSTOM_FUNCTION, function () {
            if (isset($this->input->post['register']['password'], $this->input->post['register']['password2'])) {
                return ($this->input->post['register']['password'] == $this->input->post['register']['password2']);
            } else {
                return TRUE;
            }
        }, 'The two passwords does not match');

        $this->_validator->addValidation('first_name', \Utility\Validator::PATTERN_MUST_NOT_START_WITH_NUMBER);
        $this->_validator->addValidation('first_name', \Utility\Validator::PATTERN_FORBIDDEN);
        $this->_validator->addValidation('first_name', \Utility\Validator::PATTERN_MAX_LENGTH, 50);
        $this->_validator->addValidation('first_name', \Utility\Validator::PATTERN_REQUIRED);

        $this->_validator->addValidation('last_name', \Utility\Validator::PATTERN_MUST_NOT_START_WITH_NUMBER);
        $this->_validator->addValidation('last_name', \Utility\Validator::PATTERN_FORBIDDEN);
        $this->_validator->addValidation('last_name', \Utility\Validator::PATTERN_MAX_LENGTH, 50);
        $this->_validator->addValidation('last_name', \Utility\Validator::PATTERN_REQUIRED);

        $this->_validator->addValidation('mobile_phone', \Utility\Validator::PATTERN_MAX_LENGTH, 20);
        $this->_validator->addValidation('mobile_phone', \Utility\Validator::PATTERN_FORBIDDEN);
        $this->_validator->addValidation('mobile_phone', \Utility\Validator::PATTERN_REQUIRED);

        $this->_validator->addValidation('legal', \Utility\Validator::PATTERN_MIN_VALUE, 1, 'You must read and agree with our <a href="terms" target="_blank">terms</a>');
    }

}
