<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class report extends MY_Controller {

    private $title = 'kyt-report';
    private $monthFyear =  array('APR'=> 0, 'MAY'=> 0, 'JUN'=> 0, 'JUL'=> 0, 'AUG'=> 0, 'SEP'=> 0, 'OCT'=> 0, 'NOV'=> 0, 'DEC'=> 0, 'JAN'=> 0, 'FEB'=> 0, 'MAR'=> 0);

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Kyt_model', 'kyt');
        $this->load->model('webform_model', 'wf');
        $this->load->model('inspection_model', 'ins');
    }
    
    public function index($fyear = ''){
        $fyear = $fyear != ''? $fyear : $this->setFyear();
        // echo $fyear;
        $data['title'] = $this->title;
        $data['fyear'] = $fyear;
        // $data['info']  = $this->getData($fyear);
        // var_dump($data);
        $this->views('kyt/report',$data);
    }

    // private function setFyear(){
    //     $month = date('n');
    //     $year = date('Y');
    //     if($month < 4){
    //         return $year -= 1;
    //     }
    //     return $year;
    // }

    public function getData(){
        $fyear = $_POST['fyear'];
        $cur     = $this->kyt->totalRisk($fyear);
        $prev    = $this->kyt->totalRisk($fyear-1);
        $pending = $this->kyt->totalRisk($fyear, 1);
        $topten  = $this->kyt->topten($fyear);
        
        $month = date('m');
        
        $data['ctotal']      = count($cur);
        $data['cpending']    = count($pending);
        $data['topten']      = $topten;
        $data['curCompair']  = $this->currentCompair($cur);
        $data['prevCompair'] = $this->previousCompair($prev);
        $data['month']       = date('M');
        $data['monthly']     = $this->monthly($fyear, $month);
        $data['monSec']      = $this->monthlySection($fyear, $month);
        $data['monCat']      = $this->monthlyCategory($fyear, $month);
        $data['yearSec']     = $this->yearlySection($fyear);
        $data['yearCat']     = $this->yearlyCategory($fyear);
        echo json_encode($data);
    }

    private function currentCompair($cur){
        // $data = array('APR'=> 0, 'MAY'=> 0, 'JUN'=> 0, 'JUL'=> 0, 'AUG'=> 0, 'SEP'=> 0, 'OCT'=> 0, 'NOV'=> 0, 'DEC'=> 0, 'JAN'=> 0, 'FEB'=> 0, 'MAR'=> 0);
        $data = $this->monthFyear;
        if(!empty($cur)){
            foreach($cur as $c){
                $data[strtoupper(date('M',strtotime($c->DREQDATE)))] += 1 ;
            }
        }
        return $data;
    }

    private function previousCompair($prev){
        // $data = array('APR'=> 0, 'MAY'=> 0, 'JUN'=> 0, 'JUL'=> 0, 'AUG'=> 0, 'SEP'=> 0, 'OCT'=> 0, 'NOV'=> 0, 'DEC'=> 0, 'JAN'=> 0, 'FEB'=> 0, 'MAR'=> 0);
        $data = $this->monthFyear;

        if(!empty($prev)){
            foreach($prev as $p){
                $data[strtoupper(date('M',strtotime($p->DREQDATE)))] += 1 ;
            }
        }
        return $data;
    }

    private function monthly($fyear, $month){
        // for($i=1; $i<=date('t'); $i++){
        //     $monthly[$i] = '';
        // }
        $data = array_fill(1, date('t'), 0);
        foreach($this->kyt->monthly($fyear, $month) as $m){
            $data[date('j',strtotime($m->DREQDATE))] = $m->AMOUNT;
        }
        return $data;
    }

    private function monthlySection($fyear, $month){
        $data = array();
        $section = $this->ins->getAllSection();
        foreach($section as $s){
            $data[$s->SSEC] = array_fill(1, date('t'), 0);
        }
        foreach($this->kyt->monSection($fyear, $month) as $m){
            $data[$m->SSEC][date('j',strtotime($m->DREQDATE))] = $m->AMOUNT;
        }
        return $data;
    }
    
    private function monthlyCategory($fyear, $month){
        $data = array();
        $category = $this->ins->getItems("6");
        foreach($category as $c){
            $data[$c->ITEMS_NAME] = array_fill(1, date('t'), 0);
        }
        foreach($this->kyt->monCategory($fyear, $month) as $m){
            $data[$m->ITEMS_NAME][date('j',strtotime($m->DREQDATE))] = $m->AMOUNT;
        }
        return $data;
    }
    
    private function yearlySection($fyear){
        $data = array();
        $section = $this->ins->getAllSection();
        foreach($section as $s){
            $data[$s->SSEC] = $this->monthFyear;
        }
        foreach($this->kyt->yearSection($fyear) as $m){
            if(!empty($m->MON)){
                $data[$m->SSEC][$m->MON] = $m->AMOUNT;
            }
        }
        return $data;
    }

    private function yearlyCategory($fyear){
        $data = array();
        $category = $this->ins->getItems("6");
        foreach($category as $c){
            $data[$c->ITEMS_NAME] = $this->monthFyear;
        }
        foreach($this->kyt->yearCategory($fyear) as $m){
            if(!empty($m->MON)){
                $data[$m->ITEMS_NAME][$m->MON] = $m->AMOUNT;
            }
        }
        return $data;
    }

    public function getReport(){
        $fyear = $_POST['fyear'];
        echo json_encode($this->kyt->getReport($fyear));

    }
}