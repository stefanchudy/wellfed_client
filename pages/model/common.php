<?php

class model_common extends System\Model
{

    /**
     *
     * @var \Utility\Connector $_connector
     */
    private $_connector = null;


    public function init()
    {
        $this->_connector = new \Utility\Connector;
    }

    public function getDashboardData()
    {
        return array_merge($this->getMessages(),$this->_connector->get_dashboard_data());
    }

    public function getMessages()
    {
        $result = Array(
            'msg' => Array(),
            'read' => 0,
            'message_unread' => 0,
            'message_count' => 0,
            'message_preview' => array()
        );

        $query = $this->db->query('SELECT * FROM `contact` ORDER BY `id` DESC');
        $query1 = $this->db->query('SELECT * FROM `contact_to_user` WHERE `user_id`=' . $this->user->logged['id']);

        $result['read'] = $query1->num_rows;
        $result['message_unread'] = $query->num_rows - $query1->num_rows;
        $result['message_count'] = $query->num_rows;

        $read = Array();

        foreach ($query1->rows as $row) {
            $read[] = $row['message_id'];
        }


        foreach ($query->rows as $row) {
            $result['msg'][$row['id']] = $row;
            $result['msg'][$row['id']]['read'] = in_array($row['id'], $read);
            if(count($result['message_preview'])<3){
                $result['message_preview'][]= $result['msg'][$row['id']];
            }
        }

        return $result;
    }


}
