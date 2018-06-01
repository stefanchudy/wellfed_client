<?php

class model_locations extends \System\Model
{
    private $_locations = null;

    /**
     *
     * @var \Utility\Connector $connector
     */
    private $_connector = null;

    public function init()
    {
        $this->_connector = new \Utility\Connector;
        return $this;
    }

    public function getCollection()
    {
        if ($this->_locations === null) {
            $this->_locations = $this->_connector->locations_getcollection();
        }
        return $this->_locations;
    }

    public function getUnverified()
    {
        $result = Array();
        foreach ($this->getCollection() as $k => $v) {
            if (!$v['location_verified']) {
                $result[$k] = Array(
                    'title' => $v['location_title'],
                    'description' => $v['location_description'],
                    'country' => $v['location_country'],
                    'state' => $v['location_state'],
                    'city' => $v['location_city'],
                    'address' => $v['location_address'],
                );
            }
        }

        return $result;
    }

    public function locationExists($title)
    {
        $locations = $this->getCollection();
        foreach ($locations as $value) {
            if ($value['title'] == $title) {
                return $value['id'];
            }
        }
        return NULL;
    }

    public function locationKeyExists($key){
        $collection = $this->getCollection();
        return (isset($collection[$key]));
    }

    public function loadLocation($id)
    {
        $collection = $this->getCollection();
        if (isset($collection[$id])) {
            return $collection[$id];
        }
        return null;
    }

    public function addLocation($params)
    {

        $this->_locations = null;
        return $this->_connector->locations_add($params);

    }

    public function updateLocation($params)
    {
        $id = $params['location_id'];
        $this->_locations[$id] = $this->_connector->locations_update($params);
        return $this->_locations[$id];
    }

    public function delete($id){
        return $this->_connector->locations_delete(
            array('id'=>$id)
        );
    }

    public function uploadImage($location, $image)
    {
        $location_id = $location['id'];
        if (isset($_FILES[$image]) && $_FILES[$image]['error'] == 0) {
            $upload = $_FILES[$image];
            $upload_image = $this->_connector->locations_upload_image(
                array(
                    'id' => $location_id,
                    'field' => $image,
                    'file_name' => $upload['name'],
                    'file_content' => base64_encode(file_get_contents($upload['tmp_name'])),
                )
            );
            $location[$image] = $upload_image['file'];
        }
        return $location;
    }

    public function verify($id)
    {
        if ($location = $this->loadLocation($id)) {
            $current_status = $location['location_verified'];
            return $this->updateLocation(
                array(
                    'location_id' => $id,
                    'location_verified' => (int)(!$current_status)
                )
            );
        }
        return null;

    }

}