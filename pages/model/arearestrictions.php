<?php

class model_arearestrictions extends \System\Model
{

    /**
     *
     * @var \Utility\Connector $connector
     */

    private $connector = null;
    /**
     *
     * @var \Utility\OrmDataset $_areas
     */
    private $_areas = null;

    public function init()
    {
        $this->connector = new \Utility\Connector;
        return $this;
    }

    public function getCollection()
    {
        if($this->_areas===null){
            $this->_areas = $this->connector->get_area_restrictions();
        }
        return $this->_areas;
    }

    public function add(){
        $this->_areas = null;
        return $this->connector->add_area_restrictions();
    }

    public function update($key, $params){
        $this->_areas = null;
        $this->connector->update_area_restrictions(
            array(
                'id'=> $key,
                'params' => $params
            )
        );
        return $this;
    }

    public function delete($id){
        $this->_areas = null;
        $this->connector->delete_area_restrictions(
            array(
                'id' => $id
            )
        );
        return $this;
    }

    public function recordExists($id){
        $collection = $this->getCollection();
        return isset($collection[$id]);
    }

    public function getRecord($id){
        $collection = $this->getCollection();
        return isset($collection[$id])?$collection[$id]:null;
    }

    public function getSlider(){
        $result = array();

        foreach ($this->getCollection() as $row){
            if (($row['image'] != '') && $row['city'] != '' && $row['country'] != '') {
                $result[$row['id']] = array(
                    'image' => $row['image'],
                    'sort_order' => $row['sort_order'],
                    'caption' => ($row['caption'] != '') ? $row['caption'] : $row['city'] . ' / ' . $row['country'],
                );
            }
        }

        return $result;
    }

    public function getJsonList()
    {
        $collection = $this->getCollection();
        $result = array();
        foreach ($collection as $value) {
            $result[$value['country']][$value['city']] = [];
        }

        return $result;
    }

    public function  uploadImage($id){
        $this->_areas = null;
        $this->connector->image_area_restrictions(
            array(
                'id' => $id,
                'file_name' => $_FILES['image']['name'],
                'file_content' => base64_encode(file_get_contents($_FILES['image']['tmp_name'])),
            )
        );
        return $this;
    }

}
