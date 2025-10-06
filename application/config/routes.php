<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['submit-inquiry'] = 'home/submit_inquiry';
$route['admin'] = 'admin/index';
$route['admin/update-status/(:num)'] = 'admin/update_status/$1';
$route['admin/delete/(:num)'] = 'admin/delete/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
