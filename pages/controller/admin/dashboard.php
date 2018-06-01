<?php

class Controller extends System\MainController {

    /**
     *
     * @var model_common
     */
    protected $model_common = Null;
    
    /**
     *
     * @var model_locations 
     */
    protected $model_locations = Null;
    /**
     *
     * @var model_donations
     */
    protected $model_donations = Null;

    public function init() {        

        $this->html->addHeaderTag('<script type="text/javascript" src="js/Countdown.js"></script>');

        $dashboard_data = $this->pageData['dashboard'];
//        $this->debug($dashboard_data);
//        $this->pageData['unverified_locations'] = $dashboard_data['get_dashboard_data']['locations_getcollection'];
//        $this->pageData['active_donations'] = $dashboard_data['get_dashboard_data']['donations_getcollection'];
  

        $this->html->setTitle($this->short_name . ' Administration');

        $this->renderPage('admin/dashboard');
                
    }

}
