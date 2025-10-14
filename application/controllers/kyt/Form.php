<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_form.php';
class Form extends MY_Controller {
    use _Form;
    public function __construct(){
		parent::__construct();
        // $this->session_expire();
        $this->load->model('Inspection_model', 'ins');
        $this->load->model('Kyt_model', 'kyt');
        $this->load->model('Webform_model', 'wf');

    }

    public function index(){
        if(isset($_GET["runNo"]) && $_GET["runNo"] != "") {
            $empno = isset($_GET["empno"]) ? $_GET['empno'] : '' ;
            $data['title']     = 'KYT-record';
            $data['runNo']     = $_GET["runNo"];
            $data['y2']        = $_GET["y2"];
            $data['kytfrm']    = $this->kyt->getKytFrm(array('NRUNNO' => $_GET["runNo"], 'CYEAR2' => $_GET["y2"]));
            $data['empno']     = $empno;
            $data['cextData']  = $this->getExtdata($_GET["no"], $_GET["orgNo"], $_GET["y"], $_GET["y2"], $_GET["runNo"] , $empno);
            $data['mode']      = $this->getMode($_GET["no"], $_GET["orgNo"], $_GET["y"], $_GET["y2"], $_GET["runNo"], $empno);
            if(isset($_GET["webSafety"]) && $_GET['webSafety'] != ''){
                $data['webSafety'] = 1;
                $data['title'] = 'kyt-follow';
            }else{
                $data['webSafety'] = 0;
            }
            // $data['webSafety'] = $_GET['webSafety'] != '' ? 1 : 0;
            // mode is edit
            if($data['mode'] == "2")
            {
                $this->views('kyt/form/viewapv', $data);
            }else{
                $this->views('kyt/form/view', $data);
            }
                

        }else{
            $this->session_expire();
            $data['title']   = 'KYT-record';
            $data['section'] = $this->ins->getAllSection();
            $data['grprisk'] = $this->ins->getItems("6");
            $data['formInfo'] = $this->wf->getFormMaster('ST-KYT');
            $this->views('kyt/form/create', $data);
        }

    }

    public function savekytfrm(){
        if($this->chkWorkingUser(trim($_POST['empno'])) == false){
            $this->clearform();
            $res = array(
                'status'  => false,
                'message' => "รหัสพนักงาน ผู้พบความเสี่ยงไม่ถูกต้อง",
            );
        }else if($this->chkWorkingUser(trim($_POST['empnog'])) == false)
        {
            $this->clearform();
            $res = array(
                'status'  => false,
                'message' => "รหัสพนักงาน หัวหน้ากลุ่ม KYT ไม่ถูกต้อง",
            );
        }else
        {
            $status = false;
            $data = array
             (
                'NFRMNO' => $_POST['formtype'],
                'VORGNO' => $_POST['owner'],
                'CYEAR'  => $_POST['cyear'],
                'CYEAR2' => $_POST['cyear2'],
                'NRUNNO' => $_POST['runno'],
                'EMPNORISK' => trim($_POST['empno']),
                'EMPNOHEAD' => trim($_POST['empnog']),
                'GRPNAME' => $_POST['group'],
                'SECCODE' => $_POST['sec'],
                'ITEMS_ID' => $_POST['risk'],
                'DTRISK' => $_POST['dtrisk'],
                'PROTECT' => $_POST['protect'],
                'PRECIS' =>  $_POST['precis']
            );

            $this->ins->trans_start();
            $rs = $this->ins->insert("STY_KYTFRM", $data);
            $this->ins->trans_complete();
            if ($this->ins->trans_status() === FALSE) {
                $status = false;
                $this->clearform();
            } else {
                $status = true;
            }
            $res = array(
                'status'  => $status,
                'message' => $status ? 'Save data successfully.' : 'Failed to save data.',
            );
        }
        echo json_encode($res);

    }

    public function getKytFrmWaitApv()
    {
        $empno = $_POST["empno"];
        echo json_encode($this->kyt->getKytFrmWaitApv($empno));
    }
    
   

    public function chkWorkingUser($sempno){
        $key = array(
            'CSTATUS' 	=> '1',
            'SEMPNO'	=> $sempno
        );
        $user = $this->ins->customSelect("AMECUSERALL",$key);
        if(count($user) > 0)
        {
            return true;
        }else{
            return false;
        }

    }

    private function clearform()
    {
        $key = array(
            'NFRMNO' => $_POST['formtype'],
            'VORGNO' => $_POST['owner'],
            'CYEAR'  => $_POST['cyear'],
            'CYEAR2' => $_POST['cyear2'],
            'NRUNNO' => $_POST['runno'],
        );
        $this->ins->delete("FLOW",$key);
        $this->ins->delete("FORM",$key);
    }

}