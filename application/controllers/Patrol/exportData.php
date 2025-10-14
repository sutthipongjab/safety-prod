<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class exportData extends MY_Controller {

    private $title = 'Patrol-export';

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('inspection_model', 'ins');
    }
    
    public function index($fyear = ''){
        $data['title'] = $this->title;
        $data['cla']   = $this->ins->getClass('PTC');
        $this->views('inspection/export',$data);
    }

    public function getData(){
        $sdate = $_POST['sdate'];
        $edate = $_POST['edate'];
        $sec   = $_POST['sec'];
        $class = $_POST['pClass'];

        $condition = '';
        $condition .= !empty($sdate) ? "AND P.PA_DATE >= TO_DATE('$sdate 00:00:00', 'DD/MM/YYYY HH24:MI:SS') AND PA_DATE <= TO_DATE('$edate 00:00:00', 'DD/MM/YYYY HH24:MI:SS')" : '' ;
        $condition .= !empty($class) ? "AND CLASS = '$class'" : '';
        $condition .= !empty($sec) ? "AND O.SSECCODE = '$sec'" : '';
        // $condition = array(
        //     'PA_DATE'  => !empty($sdate) ? ">= TO_DATE('$sdate 00:00:00', 'DD/MM/YYYY HH24:MI:SS') AND PA_DATE <= TO_DATE('$edate 00:00:00', 'DD/MM/YYYY HH24:MI:SS')" : '',
        //     'CLASS'    => !empty($class) ? $class : '',
        //     'SSECCODE' => !empty($sec) ? $sec : ''
        // );
        echo json_encode($this->ins->getPatrolReport($condition));
    }
}