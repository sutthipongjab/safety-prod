<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class follow extends MY_Controller {

    private $title = 'kyt-follow';

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Inspection_model', 'ins');
        $this->load->model('Kyt_model', 'kyt');
        $this->load->model('Webform_model', 'wf');
    }
    
    public function index(){
        $data['title'] = $this->title;
        $this->views('kyt/follow',$data);
    }

    public function getData(){
        $data = $this->kyt->getFrmRun();
        echo json_encode($data);
    }

}