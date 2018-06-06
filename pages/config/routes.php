<?php
$_cnf = Array();
// First run
$_cnf['install'] = 'install';

// Front
$_cnf['index'] = 'index';
$_cnf['about'] = 'front/about';
$_cnf['partners'] = 'front/partners';
$_cnf['donate'] = 'front/donate';
// blog front

$_cnf['blog'] = 'front/blog/index';
$_cnf['blog/read'] = array(
    'table' => NULL,
    'page' => 'front/blog/read',    
);
$_cnf['blog/search'] = 'front/blog/search';
$_cnf['profile'] = 'front/profile';

$_cnf['locations/add'] = 'front/locations/locations_add';
$_cnf['locations/edit'] = 'front/locations/locations_edit';
$_cnf['locations/view'] = 'front/locations/locations_edit';

$_cnf['donations/add'] = 'front/donations/donations_add';
$_cnf['donations/edit'] = 'front/donations/donations_edit';
$_cnf['donations/delete'] = 'front/donations/donations_delete';
$_cnf['donations/delete_booking'] = 'front/donations/delete_booking';

$_cnf['search'] = 'front/search';
// Admin pages
$_cnf['admin/dashboard'] = 'admin/dashboard';

$_cnf['admin/general'] = 'admin/system/general';
$_cnf['admin/legal/food-recipient-agreement'] = 'admin/legal/legal';
$_cnf['admin/legal/food-donation-agreement'] = 'admin/legal/legal';
$_cnf['admin/legal/terms-of-website'] = 'admin/legal/legal';
$_cnf['admin/legal/privacy-policy'] = 'admin/legal/legal';
$_cnf['admin/legal/food-safety-and-hygiene'] = 'admin/legal/legal';

$_cnf['admin/users'] = 'admin/users/users';
$_cnf['admin/users/add'] = 'admin/users/users_add';
$_cnf['admin/users/edit'] = 'admin/users/users_edit';
$_cnf['admin/users/delete'] = 'admin/users/user_del';
$_cnf['admin/users/upgrade'] = 'admin/users/user_upgrade';
$_cnf['admin/users/reject'] = 'admin/users/user_reject';

$_cnf['logout'] = 'admin/logout';

// Admin pages -> System -> Messages
$_cnf['admin/messages'] = 'admin/system/messages';
$_cnf['admin/messages/read'] = 'admin/system/messages_read';
$_cnf['admin/messages/delete'] = 'admin/system/messages_del';
// Locations
$_cnf['admin/locations'] = 'admin/locations/locations';
$_cnf['admin/locations/add'] = 'admin/locations/locations_add';
$_cnf['admin/locations/edit'] = 'admin/locations/locations_edit';
$_cnf['admin/locations/verify'] = 'admin/locations/locations_verify';
$_cnf['admin/locations/delete'] = 'admin/locations/locations_delete';
// Donations
$_cnf['admin/donations'] = 'admin/donations/donations';
$_cnf['admin/donations/add'] = 'admin/donations/donations_add';
$_cnf['admin/donations/edit'] = 'admin/donations/donations_edit';
$_cnf['admin/donations/delete'] = 'admin/donations/donations_delete';
//booking
$_cnf['admin/booking/reset'] = 'admin/booking/reset';
$_cnf['admin/booking/delete'] = 'admin/booking/delete';
$_cnf['admin/booking/taken'] = 'admin/booking/taken';
//booking - front
$_cnf['booking/reset'] = 'front/booking/reset';
$_cnf['booking/delete'] = 'front/donations/delete_booking';
$_cnf['booking/taken'] = 'front/booking/taken';

// Ajax 
$_cnf['ajax/get_locations'] = 'ajax/get_locations';
$_cnf['ajax/get_local_time'] = 'ajax/get_local_time';
$_cnf['ajax/search'] = 'ajax/search';
$_cnf['ajax/get_area_restrictions'] = 'ajax/get_area_restrictions';
//front

$_cnf['contact'] = 'front/contact';

$_cnf['food-recipient-agreement'] = 'front/legal/legal';
$_cnf['food-donation-agreement'] = 'front/legal/legal';
$_cnf['terms-of-website'] = 'front/legal/legal';
$_cnf['privacy-policy'] = 'front/legal/legal';
$_cnf['food-safety-and-hygiene'] = 'front/legal/legal';

// 2ndary data
$_cnf['admin/food-types']='admin/pages/food_types';
$_cnf['admin/location-types']='admin/pages/location_types';
//slider / working areas
$_cnf['admin/working-areas'] = 'admin/slider/slider';
$_cnf['admin/working-areas/add'] = 'admin/slider/slider_add';
$_cnf['admin/working-areas/edit'] = 'admin/slider/slider_edit';
$_cnf['admin/working-areas/del'] = 'admin/slider/slider_del';

//blog
$_cnf['admin/blog'] = 'admin/blog/index';
$_cnf['admin/blog/add'] = 'admin/blog/add';
$_cnf['admin/blog/edit'] = 'admin/blog/edit';
$_cnf['admin/blog/del'] = 'admin/blog/del';

//$_cnf['forgotten_password'] = 'front/forgotten_password';

//$_cnf['social-login'] = 'front/social_login';

return $_cnf;