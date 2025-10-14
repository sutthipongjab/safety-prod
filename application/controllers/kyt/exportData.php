<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class exportData extends MY_Controller {

    private $title = 'kyt-export';

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Kyt_model', 'kyt');
        $this->load->model('webform_model', 'wf');
        $this->load->model('inspection_model', 'ins');
    }
    
    public function index($fyear = ''){
        $data['title'] = $this->title;
        $this->views('kyt/export',$data);
    }

    public function getSectionEmp()
    {
        $empno = $_POST["empno"];
        echo  json_encode($this->kyt->getSectionEmp($empno));
    }

    public function getData(){
        $sdate = $_POST['sdate'];
        $edate = $_POST['edate'];
        $sec   = $_POST['sec'];

        $condition = '';
        $condition .= !empty($sdate) ? "AND DREQDATE >= TO_DATE('$sdate 00:00:00', 'DD/MM/YYYY HH24:MI:SS') AND DREQDATE <= TO_DATE('$edate 00:00:00', 'DD/MM/YYYY HH24:MI:SS')" : '' ;
        $condition .= !empty($sec) ? "AND SECCODE = '$sec'" : '';
        echo json_encode($this->kyt->getKytReport($condition));
    }
}