<?php

class model_foods extends System\Model
{

    /**
     *
     * @var \Utility\Connector $connector
     */
    private $connector = null;

    private $max_parents = 2;

    private $_foodsList = null;


    public function init()
    {
        $this->max_parents = $this->db_settings->get('foods_max_level', 3);
        $this->connector = new \Utility\Connector;
        return $this;
    }

    public function getSelector($selected = null, $id = 'food_type_id', $name = 'food_type_id')
    {
        $array = $this->getArray();
        $result = '<select id="' . $id . '" name="' . $name . '" class="form-control">';
        $isSelected = ($selected === 0) ? ' selected="selected"' : '';
        $result .= '<option value="0"' . $isSelected . '>Not specified</option>';

        foreach ($array as $key => $value) {
            $isSelected = ($selected == $key) ? ' selected="selected"' : '';
            $result .= '<option value="' . $key . '"' . $isSelected . '>' . $value . '</option>';
        }

        $result .= '</select>';
        return $result;
    }

    public function getArray()
    {
        $list = $this->get_list();

        $result = Array();
        foreach ($list as $key => $value) {
            $result[$value['id']] = $this->getFullPath($list, $value['id']);
        }
        asort($result);
        return $result;
    }

    public function get_list()
    {
        if ($this->_foodsList === null) {
            $this->_foodsList = $this->connector->get_food_list();
        }
        return $this->_foodsList;
    }

    private function getFullPath($list, $id)
    {
        $result = $list[$id]['title'];

        if ($list[$id]['parent_id'] != 0) {
            $current_id = $list[$id]['parent_id'];

            while ($current_id != 0) {
                $result = $list[$current_id]['title'] . ' > ' . $result;
                $current_id = $list[$current_id]['parent_id'];
            }
        }

        return $result;
    }

    public function exists($title)
    {
        foreach ($this->get_list() as $item) {
            if ($item['title'] == $title) {
                return true;
            }
        }
        return false;
    }

    public function getFullType($id)
    {
        $array = $this->getArray();
        return (isset($array[$id]) ? $array[$id] : null);
    }

    public function tree()
    {
        $new = array();
        foreach ($this->get_list() as $a) {
            $new[$a['parent_id']][] = $a;
        }
        return [
            [
                'id' => 0,
                'parent_id' => -1,
                'title' => 'All food types',
                'description' => NULL,
                'system' => TRUE,
                'children' => $this->createTree($new, isset($new[0]) ? $new[0] : array())
            ]
        ];
    }

    private function createTree($list, $parent)
    {
        $tree = array();
        foreach ($parent as $k => $l) {
            if (isset($list[$l['id']])) {
                $l['children'] = $this->createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }

    public function save($parent_id, $title, $description)
    {
        $this->_foodsList = null;
        $this->connector->save_food_type(
            array(
                'parent_id' => (int)$parent_id,
                'title' => $this->db->escape($title),
                'description' => $this->db->escape($description)
            )
        );
        return $this;
    }

    public function update($id, $params){
        $this->_foodsList = null;
        $this->connector->update_food_type(
            array(
                'id'=> $id,
                'params' => $params
            )
        );
        return $this;
    }

    public function delete($id){
        $this->_foodsList = null;
        $this->connector->delete_food_type(
            array(
                'id'=> $id
            )
        );
        return $this;
    }

    private function count_children($node)
    {
        $counter = 0;
        if (isset($node['children'])) {
            foreach ($node['children'] as $value) {
                $counter++;
                if (isset($value['children'])) {
                    $counter += $this->count_children($value['children']);
                }
            }
        }

        return $counter;
    }

    private function count_parents_array($list, $id)
    {
        $_id = $id;
        $counter = 1;
        while ((isset($list[$_id]) && $list[$_id]['parent_id'] != 0)) {
            $counter++;
            $_id = $list[$_id]['parent_id'];
        }
        return $counter;
    }

    public function build_html_tree($tree = NULL)
    {
        $list = $this->get_list();
        $root = ($tree === NULL);
        if ($root) {
            $tree = $this->tree();
        }

        $result = '<ul' . ($root ? ' class="root"' : '') . '>';

        foreach ($tree as $node) {
            $level = $this->count_parents_array($list, $node['id']);
            $check_id = 'food_type_' . $node['id'];
            $result .= '<li>';
            if (!$root && isset($node['children'])) {
                $result .= '   <input class="ft-node-expander" type="checkbox" id="' . $check_id . '" data-id="' . $node['id'] . '"/>';
                $result .= '   <label for="' . $check_id . '" title="' . $node['description'] . '">';
            } else {
                $result .= '   <label>';
            }
            $result .= $node['title'];
            $result .= '      <span class="pull-right">';
            $children_count = $this->count_children($node);
            $result .= '<span class="button-bar">';
            if ($level <= ($this->max_parents - 1)) {
                $result .= ' <a href="#" class="btn btn-xs btn-default btn-food-add" data-action="add" data-parent="' . $node['id'] . '">Add</a>';
            }
            if($node['parent_id']!=-1){
                $result .= ' <a href="#" class="btn btn-xs btn-success btn-food-edit" data-id="' . $node['id'] . '" data-title="' . $node['title'] . '" data-description="' . $node['description'] . '">Edit</a>';
            }

            if ($children_count == 0 && ($node['parent_id']!=-1)) {
                $result .= ' <a href="#" class="btn btn-xs btn-danger btn-food-delete" data-id="' . $node['id'] . '">Delete</a>';
            }
            $result .= '</span>';

            $result .= (($children_count != 0) ? '<span class="badge">' . $children_count . '</span>' : '');
            $result .= '      </span>';
            $result .= '   </label>';

            if (isset($node['children'])) {
                $result .= $this->build_html_tree($node['children']);
            }

            $result .= '</li>';
        }
        $result .= '</ul>';

        return $result;
    }

}
