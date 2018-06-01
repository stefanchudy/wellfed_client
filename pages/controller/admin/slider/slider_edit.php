<?php

class Controller extends System\MainController {

    /**
     *
     * @var model_arearestrictions
     */
    protected $model_arearestrictions = Null;

    public function init() {
        $api_url = $this->db_settings->get(API_URL).'/';
        if (isset($this->input->get['id'])) {
            $id = (int) $this->input->get['id'];

            $this->loadModel('arearestrictions');

            $this->_uploadImage($id);

            if ($this->model_arearestrictions->recordExists($id)) {
                $slide = $this->model_arearestrictions->getRecord($id);

                $this->pageData['sort_order'] = $slide['sort_order'];
                $this->pageData['caption'] = $slide['caption']; // to do : delete this line
                $this->pageData['country'] = $slide['country'];
                $this->pageData['city'] = $slide['city'];

                $this->pageData['image'] = $slide['image'] != '' ? $api_url.$slide['image'] . '?no-cache=' . uniqid() : 'https://placeholdit.imgix.net/~text?txtsize=24&txt=No%20image%20uploaded&w=276&h=210';

                if (isset($this->input->post['sort_order'])) {
                    $this->pageData['sort_order'] = (int) $this->input->post['sort_order'];
                    $this->pageData['caption'] = $this->input->post['caption']; // TO DO : Delete this field
                    $this->pageData['country'] = $this->input->post['country'];
                    $this->pageData['city'] = $this->input->post['city'];

                    $this->_validateForm();
                    
                    if(empty($this->errors)){
                        $this->model_arearestrictions->update($id, array(
                            'sort_order' => $this->pageData['sort_order'],
                            'caption' => $this->pageData['caption'],
                            'country' => $this->pageData['country'],
                            'city' => $this->pageData['city'],
                        ));
                    }
                }
            } else {
                $this->redirect('admin/working-areas');
            }
        } else {
            $this->redirect('admin/working-areas');
        }

        $this->html->setTitle($this->short_name . ' | Edit slide');
        $this->renderPage('admin/slider/slider_edit');
    }

    private function _validateForm() {
        $this->_validateLocality();
        return $this;
    }

    private function _validateLocality() {
        $geoCode = $this->_geoCode($this->pageData['country'] . ' ' . $this->pageData['city']);
        $country_valid = false;
        $city_valid = false;
        if (!empty($geoCode['results'])) {
            $result = $geoCode['results'][0];
            foreach ($result['address_components'] as $component) {
                if (in_array('country', $component['types'])) {
                    $this->pageData['country'] = $component['long_name'];
                    $country_valid = true;
                }
                if (in_array('locality', $component['types'])) {
                    $this->pageData['city'] = $component['long_name'];
                    $city_valid = true;
                }
                if ($city_valid && $country_valid) {
                    return TRUE;
                }
            }
        }
        $this->errors['locality'] = 'Invalid location name. Cannot find it on the map.';
        return false;
    }

    private function _geoCode($locality) {
        $url = 'https://maps.google.com/maps/api/geocode/json';

        $header = array();
        $header[] = 'Accept: application/json';
        $header[] = 'Content-type: application/json';

        $params = http_build_query(array('address' => $locality));

        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url . '?' . $params);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_HTTPGET, true);
        curl_setopt($crl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, 0);
        $rest = curl_exec($crl);

        if ($rest === false) {
            echo json_encode(Array(
                'curl_error' => curl_error($crl)
            ));
            exit;
        }
        curl_close($crl);

        $response = json_decode($rest, TRUE);

        return $response;
    }

    private function _uploadImage($id){
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
                    $this->model_arearestrictions->uploadImage($id);
                }
            }
        }
        return $this;
    }

}
