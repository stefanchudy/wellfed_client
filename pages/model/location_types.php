<?php

class model_location_types extends System\Model
{

    /**
     *
     * @var \Utility\Connector $connector
     */
    private $_connector = null;

    private $location_types = null;

    public function init()
    {
        $this->_connector = new \Utility\Connector;
        return $this;
    }

    public function get()
    {
        if ($this->location_types === null) {
            $this->location_types = $this->_connector->location_types_get();
        }
        return $this->location_types;
    }

    public function get_name($id)
    {
        $result = null;
        $location_types = $this->get();
        if (isset($location_types[$id])) {
            $result = $location_types[$id]['title'];
        }
        return $result;
    }

    public function add($_title, $_description)
    {
        $this->location_types = null;
        $this->_connector->location_types_add(
            array(
                'title' => $_title,
                'description' => $_description,
            )
        );
        return $this;
    }

    public function update($id, $title, $description)
    {
        $this->location_types = null;
        $this->_connector->location_types_update(
            array(
                'id' => $id,
                'title' => $title,
                'description' => $description
            )
        );
        return $this;
    }

    public function delete($id)
    {
        $this->location_types = null;
        $this->_connector->location_types_delete(
            array(
                'id'=>$id
            )
        );
        return $this;
    }

    public function getAdminHtml() {
        $result = '<div class="list-group item-list">';
        $location_types = $this->get();
        If (count($location_types)) {
            foreach ($location_types as $item) {
                $result .= '<div class="list-group-item">';
                $result .= '<span class="pull-right">';
                $result .= '<div class="item-control">';
                $result .= '<a class="btn btn-success btn-xs btn-type-edit" data-id="' . $item['id'] . '" data-title="' . $item['title'] . '" data-description="' . $item['description'] . '">Edit</a> ';
                $result .= '<a class="btn btn-danger btn-xs btn-type-delete" data-id="' . $item['id'] . '">Delete</a>';
                $result .= '</div>';
                $result .= '</span>';
                $result .= '<strong>' . $item['title'] . '</strong>';
                $result .= '<br>';
                $result .= '<em>' . $item['description'] . '</em>';

                $result .= '</div>';
            }
        } else {
            $result .= '<em>No records in the database</em>';
        }
        $result .= '</div>';

        return $result;
    }

    public function getSelector($id, $selected = 0) {
        $location_types = $this->get();
        $result = '<select id="' . $id . '" name="' . $id . '" class="form-control">';
        $result .= '<option value="0"' . (($selected == 0) ? ' selected="selected"' : '') . '>Choose a location type from the list</option>';
        foreach ($location_types as $type) {
            $result .= '<option value="' . $type['id'] . '"' . (($type['id'] == $selected) ? ' selected="selected"' : '') . '>' . $type['title'] . '</option>';
        }
        $result .= '</select>';
        return $result;
    }

    public function exists($title) {
        $location_types = $this->get();
        foreach ($location_types as $value) {
            if ($value['title'] == $title) {
                return $value['id'];
            }
        }
        return NULL;
    }
}
