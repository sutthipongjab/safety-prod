<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class report extends MY_Controller {

    private $title = 'Patrol-report';
    private $monthFyear =  array('APR'=> 0, 'MAY'=> 0, 'JUN'=> 0, 'JUL'=> 0, 'AUG'=> 0, 'SEP'=> 0, 'OCT'=> 0, 'NOV'=> 0, 'DEC'=> 0, 'JAN'=> 0, 'FEB'=> 0, 'MAR'=> 0);

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('inspection_model', 'ins');
    }
    
    public function index($fyear = ''){
        $fyear = $fyear != ''? $fyear : $this->setFyear();
        $data['title'] = $this->title;
        $data['fyear'] = $fyear;
        $this->views('inspection/report',$data);
    }

    public function getData(){
        $fyear = $_POST['fyear'];
        $compData = $this->ins->compClass($fyear);

        $data['compClass'] = $this->compClass($compData);
        
        $month = date('m');
        echo json_encode($data);
    }

    private function compClass($comp){
        $data = array(
            'classA' => $this->monthFyear,
            'classB' => $this->monthFyear,
        );
        if(!empty($comp)){
            foreach($comp as $c){
                $c->TYPE_NAME == 'A' ? $data['classA'][$c->MON] = $c->AMOUNT : $data['classB'][$c->MON] = $c->AMOUNT;
            }
        }
        return $data;
    }

    public function getDataDept(){
        $month = $_POST['month'];
        $dept  = $_POST['dept'];
        $fyear = $_POST['fyear'];
        foreach($dept as $d){
            $data[$d['SDEPT']]['classA'] = 0;
            $data[$d['SDEPT']]['classB'] = 0;
        }
        $dataDept = $this->ins->getDataDept($month, $fyear);
        if(!empty($dataDept)){
            foreach($dataDept as $d){
                $d->TYPE_NAME == 'A' ? $data[$d->SDEPT]['classA'] = $d->AMOUNT : $data[$d->SDEPT]['classB'] = $d->AMOUNT;
            }
        }
        echo json_encode($data);
    }

    public function getClass(){
        $fyear = $_POST['fyear'];
        $month = $_POST['month'];
        $type  = $_POST['type'];
        echo json_encode($this->ins->getClassByMonth($month, $fyear, $type));
    }

    public function getDataSec(){
        $fyear = $_POST['fyear'];
        $month = $_POST['month'];
        $sec   = $_POST['sec'];
        $dataSec = $this->ins->getDataSec($month, $fyear, $sec);
        $data = array();
        if(!empty($dataSec)){
            foreach($dataSec as $d){
                if(!empty($d->CLASS)){
                    $data[$d->SSEC][$d->CLASS] = $d->AMOUNT;
                }else{
                    $data[$d->SSEC]['A'] = 0;
                    $data[$d->SSEC]['B'] = 0;
                }
                if (!isset($data[$d->SSEC]['A'])) {
                    $data[$d->SSEC]['A'] = 0;
                }
                if (!isset($data[$d->SSEC]['B'])) {
                    $data[$d->SSEC]['B'] = 0;
                }
            }
        }
        echo json_encode($data);
    }

    public function getYearlySec(){
        $fyear = $_POST['fyear'];
        $month = $_POST['month'];
        $sec   = $_POST['sec'];
        $dataSec = $this->ins->getYearlySec($month, $fyear, $sec);
        $data = array(
            'A' => $this->monthFyear,
            'B' => $this->monthFyear,
        );
        if(!empty($dataSec)){
            foreach($dataSec as $d){
                $data[$d->CLASS][$d->MON] = $d->AMOUNT;
            }
        }
        $data['category'] = $this->ins->getCatByMonth($month, $fyear, $sec);
        $data['categoryA'] = $this->ins->getCatByMonth($month, $fyear, $sec, 'A');
        $data['categoryB'] = $this->ins->getCatByMonth($month, $fyear, $sec, 'B');
        echo json_encode($data);
    }
}