<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class test extends MY_Controller {


    public function __construct(){
		parent::__construct();
        $this->session_expire();
    }
    

    public function index(){
        $data['title'] = 'test';
        $this->views('test',$data);
    }
}