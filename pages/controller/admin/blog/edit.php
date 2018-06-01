<?php

class Controller extends System\MainController {

    private $_id = null;

    /**
     *
     * @var model_blog
     */
    protected $model_blog = Null;

    public function init() {

        $this->pageData['api_url'] = $this->db_settings->get(API_URL).'/';
        if (isset($this->input->get['id'])) {
            $this->_id = (int) $this->input->get['id'];
            $this->loadModel('blog');

            
            $this->_setValidation();
            $this->html->addHeaderTag('<link href="style/jquery-te-1.4.0.css" rel="stylesheet">');
            $this->html->addHeaderTag('<script type="text/javascript" src="js/jquery-te-1.4.0.min.js" charset="utf-8"></script>');

            $request = $this->model_blog->getAdminBlogPost($this->_id);
            if (!empty($request)) {
                $request = $this->_uploadImage($request);

                $this->pageData['id'] = $this->_id;

                if (count($this->input->post)) {
                    $this->errors = $this->validator->validateAll($this->input->post);
                    if (empty($this->errors)) {
                        $request = $this->model_blog->updateRecord($this->_id, $this->input->post);
                    }
                }

                $this->pageData['postData'] = $request;
            } else {
                $this->redirect('admin/blog');
            }
        } else {            
            $this->redirect('admin/blog');
        }

        $this->html->setTitle($this->getSiteShortName() . ' Administration | Edit Blog Post');
        $this->renderPage('admin/blog/details');
    }

    private function _setValidation() {
        $this->validator->clear();
        $this->validator->addValidation('title', \Utility\Validator::PATTERN_REQUIRED);
        $this->validator->addValidation('title', \Utility\Validator::PATTERN_FORBIDDEN);
        $this->validator->addValidation('title', \Utility\Validator::PATTERN_MIN_LENGTH, 5);
        $this->validator->addValidation('title', \Utility\Validator::PATTERN_MUST_NOT_START_WITH_NUMBER);

        $this->validator->addValidation('content', \Utility\Validator::PATTERN_REQUIRED);
        $this->validator->addValidation('content', \Utility\Validator::PATTERN_MIN_LENGTH, 30);
    }

    private function _uploadImage($request)
    {
        $upload_errors = array(
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
        );
        $field = 'image';
        if (isset($_FILES[$field])) {
            $error_code =  $_FILES[$field]['error'];

            if (isset($upload_errors[$error_code])) {
                $this->errors[$field] = $upload_errors[$error_code];
            } else {
                if($error_code==0){
                    $request = $this->model_blog->uploadImage($request);
                }
            }
        }

        return $request;
    }

}
