<?php

class Controller extends System\MainController {

    /**
     *
     * @var model_location_types
     */
    protected $model_location_types = Null;

    /**
     *
     * @var model_locations
     */
    protected $model_locations = Null;

    /**
     *
     * @var model_users 
     */
    protected $model_users = Null;

    public function init() {
        $this->loadModel('location_types');
        $this->loadModel('locations');

        $types = $this->model_location_types->get();
        $locations = $this->model_locations->getCollection();
        $output_data = Array();

        if ($this->user && $this->user->logged) {

            foreach ($locations as $id => $location) {
                if ($this->user->logged['id'] != $location['user_id']) {
                    continue;
                }
                if ($location['location_verified']) {
                    $user_link = '';
                    $output_data[$id] = Array(
                        'id' => $location['id'],
                        'title' => $location['location_title'],
                        'country' => $location['location_country'],
                        'city' => $location['location_city'],
                        'state' => $location['location_state'],
                        'address' => $location['location_address'],
                        'logo' => $this->db_settings->get(API_URL) . '/'.$location['location_logo'],
                        'geo' => Array(
                            'lat' => $location['location_geo_lat'],
                            'lng' => $location['location_geo_lng'],
                        ),
                        'user' => Array(
                            'id' => $location['user_id'],
                            'email' => $location['user']['email'],
                            'link' => $location['user']['link']
                        ),
                        'type' => Array(
                            'id' => $location['location_type'],
                            'title' => $types[$location['location_type']]['title'],
                        )
                    );
                }
            }
        }


        header("HTTP/1.1 200 OK");
        header('Content-Type: application/json');

        echo json_encode($output_data);
    }

}
