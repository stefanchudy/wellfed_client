<?php

class model_blog extends \System\Model
{

    /**
     *
     * @var \Utility\Connector $connector
     */

    const CALL_MODE_FRONT = 0;
    const CALL_MODE_ADMIN = 1;

    private $_callMode = 0;
    private $_filter =  null;

    private $_callModes = array(
        self::CALL_MODE_FRONT => 'front',
        self::CALL_MODE_ADMIN => 'admin'
    );

    private $connector = null;

    public function init()
    {
        $this->connector = new \Utility\Connector;
        return $this;
    }

    public function setCallMode($callMode){
        $this->_callMode = $callMode;
        return $this;
    }

    public function setFilter($filter=null){
        $this->_filter = $filter;
        return $this;
    }

    public function getCollection()
    {
        return $this->connector->get_blog_collection(
            array(
                'call_mode' => $this->_callModes[$this->_callMode],
                'filter' => $this->_filter
            )
        );
    }

    public function getAdminBlogPost($id){
        return $this->connector->get_blog_post_admin(
            array(
                'id' => $id
            )
        );
    }

    public function getBlogPost($id){
        return $this->connector->get_blog_post(
            array(
                'id' => $id
            )
        );
    }

    public function searchInBlog($search_string){
        return $this->connector->search_blog(
            array(
                'search_string' => base64_encode(trim(htmlspecialchars($search_string)))
            )
        );
    }

    public function updateRecord($id, $data){
        return $this->connector->update_blog_post(
            array(
                'id' => $id,
                'data' => $data
            )
        );
    }

    public function uploadImage($post){
        $post_id = $post['id'];

        return $this->connector->set_post_image(
            array(
                'id'=> $post_id,
                'file_name' => $_FILES['image']['name'],
                'file_content' => base64_encode(file_get_contents($_FILES['image']['tmp_name'])),
            )
        );
    }

    public function addBlogPost($title){
        return $this->connector->add_blog_post(
            array(
                'title' => $title
            )
        );
    }

    public function deleteRecord($id){
        return $this->connector->del_blog_post(
            array(
                'id' => $id
            )
        );
    }
}
