<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_form.php';
class follow extends MY_Controller {
    use _Form;
    private $title = 'electric-follow';

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Webform_model', 'wf');
        $this->load->model('form_model', 'frm');
        $this->load->model('electric_model', 'elec');
    }
    
    public function index(){
        $data['title'] = $this->title;
        $this->views('electric/follow',$data);
    }

    public function getData(){
        $monthYear = $_POST['monthYear'];
        $start = date('j/n/Y', strtotime($monthYear));
        $end = date('t/n/Y', strtotime($monthYear));
        // echo $start.' '.$end;
        $data = $this->frm->getFormByMonth($start, $end, 'ET');
        if(!empty($data)){
            foreach($data as $key => $d){
                $data[$key]->FORMNO = $this->toFormNumber($d->NFRMNO, $d->VORGNO, $d->CYEAR, $d->CYEAR2, $d->NRUNNO);
        //         $data[$key]->PA_DATE = date('j/n/Y', strtotime($d->PA_DATE));
            }
        }
        echo json_encode($data);
    }

    

}