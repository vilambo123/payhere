<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['submit-inquiry'] = 'home/submit_inquiry';
$route['download'] = 'download/index';

// Admin routes
$route['admin'] = 'admin/index';
$route['admin/view/(:num)'] = 'admin/view/$1';
$route['admin/update/(:num)'] = 'admin/update/$1';
$route['admin/update-status/(:num)'] = 'admin/update_status/$1';
$route['admin/delete/(:num)'] = 'admin/delete/$1';

// Settings routes
$route['settings'] = 'settings/index';
$route['settings/update'] = 'settings/update';
$route['settings/update-loan-type/(:num)'] = 'settings/update_loan_type/$1';

// Language routes
$route['language/switch'] = 'language/switch_language';

// API routes
$route['api'] = 'api/index';
$route['api/inquiries'] = 'api/inquiries';
$route['api/mark_exported'] = 'api/mark_exported';
$route['api/reset_export'] = 'api/reset_export';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
