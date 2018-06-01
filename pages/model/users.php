<?php
class model_users extends \System\Model {
    private $_users = null;

    /**
     *
     * @var Utility\Connector $_connector
     */
    private $_connector = null;

    public function init(){
        $this->_connector = new Utility\Connector();
    }

    public function getUsers(){
        if($this->_users===null){
            $this->_users = $this->_connector->users_get_collection();
        }
        return $this->_users;
    }

    public function getUserSelector($id, $selected = NULL) {
        $result = '';
        $users = $this->getUsers();
        if ($selected === NULL) {
            $selected = $this->user->logged['id'];
        }
        $result.='<select name="' . $id . '" id="' . $id . '" class="form-control">';

        foreach ($users as $user) {
            $user_access = $user['access'][0];
            if ($user['data']['ban'])
                continue;
            $selection = ($user['id'] == $selected ? ' selected="selected"' : '');
            $result.='<option value="' . $user['id'] . '"' . $selection . '>' . $user['email'] . (($user['id'] == $this->user->logged['id']) ? ' (you)' : '') . '</option>';
        }
        $result.='</select>';
        return $result;
    }

    public function getUserById($id){
        $collection = $this->getUsers();
        return isset($collection[$id])?$collection[$id]:null;
    }

}