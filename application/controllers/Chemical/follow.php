<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_form.php';
class follow extends MY_Controller {
    use _Form;
    private $title = 'chemical-follow';

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Webform_model', 'wf');
        $this->load->model('form_model', 'frm');
        $this->load->model('chemical_model', 'che');
    }
    
    public function index($fyear = ''){
        $fyear = $fyear != ''? $fyear : $this->setFyear();
        $data['fyear'] = $fyear;
        $data['title'] = $this->title;
        $this->views('chemical/follow',$data);
    }

    public function getFormData(){
        $fyear = $this->input->post('fyear');
        // echo $start.' '.$end;
        // $data = $this->che->getFormData(["TO_CHAR(CREATE_DATE, 'YYYY') =" => $fyear]);
        $data = $this->che->getFollow($fyear);
        if(!empty($data)){
            foreach($data as $key => $d){
                $data[$key]->FORMNO = $this->toFormNumber($d->NFRMNO, $d->VORGNO, $d->CYEAR, $d->CYEAR2, $d->NRUNNO);
            }
        }
        echo json_encode($data);
    }

    

}