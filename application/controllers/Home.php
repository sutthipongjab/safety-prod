<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_file.php';
class Home extends MY_Controller {
    use _File;
    
    function __construct(){
		parent::__construct();
        $this->session_expire();
    }

    public function index(){
        // echo "Hello World";
        // var_dump($_SESSION);
        // $data =  array(
        //     'amecweb' => 'https://' . $_SERVER['HTTP_HOST'],
        //     'webflow' => 'http://webflow.mitsubishielevatorasia.co.th/',
        //     'webform' => 'http://webflow.mitsubishielevatorasia.co.th/formtest/',
        //     'procure' => 'http://webflow.mitsubishielevatorasia.co.th/formtest/procurement/procurement.asp',
        // );
        // $data['Cmenu'] = isset($_SESSION['menu']) ? count($_SESSION['menu']) : 0;
        // var_dump($data);
        $this->views('index');
    }

    public function news(){
        $this->views('news');
    }
}