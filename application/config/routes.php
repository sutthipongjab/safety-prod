<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['default_controller'] = 'home';
// $route['404_override'] = '';
$route['404_override'] = 'errors/page_missing';
$route['translate_uri_dashes'] = FALSE;