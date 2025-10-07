<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['submit-inquiry'] = 'home/submit_inquiry';
$route['download'] = 'download/index';
$route['admin'] = 'admin/index';
$route['admin/update-status/(:num)'] = 'admin/update_status/$1';
$route['admin/delete/(:num)'] = 'admin/delete/$1';
$route['settings'] = 'settings/index';
$route['settings/update'] = 'settings/update';
$route['settings/update-loan-type/(:num)'] = 'settings/update_loan_type/$1';
$route['language/switch'] = 'language/switch_language';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
