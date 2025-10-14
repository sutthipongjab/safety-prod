<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_form.php';
require_once APPPATH.'controllers/_file.php';
class webflow extends MY_Controller {

    use _Form;
    use _File;
    public function __construct(){
		parent::__construct();
        $this->load->model('Inspection_model', 'ins');
        $this->load->model('Area_model', 'area');
        $this->load->model('Webform_model', 'wf');
        $this->upload_path = "//amecnas/AMECWEB/File/" .($this->_servername()=='amecweb' ? 'production' : 'development') ."/safety/image/Patrol/";
    }
    
    // public function index($NFRMNO='', $VORGNO='', $CYEAR='', $CYEAR2='', $NRUNNO='', $empno=''){
    public function index($formno = ''){
        if($formno != ''){
            $master = $this->wf->getFormMaster('ST-INP');
            $form = array(
                'NFRMNO' => $master[0]->NNO,
                'VORGNO' => $master[0]->VORGNO,
                'CYEAR'  => $master[0]->CYEAR,
                'CYEAR2' => '20'.substr($formno,6,2),
                'NRUNNO' => (int) substr($formno,9,6)
            );
            $empno = '';
        }else{
            $form = array(
                'NFRMNO' => $_GET['no'],
                'VORGNO' => $_GET['orgNo'],
                'CYEAR'  => $_GET['y'],
                'CYEAR2' => $_GET['y2'],
                'NRUNNO' => $_GET['runNo']
            );
            $empno = $_GET['empno'];
        }
        $EmpFlow  = $this->wf->getEmpFlow($form, $empno);
        $approve  = false;
        $cextData = null;
        if(!empty($EmpFlow)){
            $approve = true;
            $cextData = $EmpFlow[0]->CEXTDATA;
        }
        $data['form']    = $form;
        $data['website'] = 'webflow';
        $data['title']   = 'inspection';
        $data['empno']   = $empno;
        $data['approve'] = $approve;
        $data['cextData'] = $cextData;
        // $data['employee'] = $this->ins->getEmployee();
        // echo json_encode($data);
        // $data['patrol']  = $patrol['data'];
        $this->views('inspection/inspectionForm',$data);
    }

       /**
     * Get patrol.
     * @param string $NFRMNO The form number.
     * @param string $VORGNO The organization number.
     * @param string $CYEAR The year.
     * @param string $CYEAR2 The year2.
     * @param string $NRUNNO The run number.
     * @return array The patrol data.
     */
    public function getPatrol($NFRMNO, $VORGNO, $CYEAR, $CYEAR2, $NRUNNO){
        $form = array(
            'NFRMNO' => $NFRMNO,
            'VORGNO' => $VORGNO,
            'CYEAR'  => $CYEAR,
            'CYEAR2' => $CYEAR2,
            'NRUNNO' => $NRUNNO
        );
        $data = $this->ins->getPatrol($form);
        $empData = array();
        if(!empty($data)){
            foreach($data as $key => $d){
                $data[$key]->PA_DATE = $this->conVdateToMoment($data[$key]->PA_DATE);
                $data[$key]->baseURL = $this->conVBase64($data[$key]->IMAGE_PATH.$data[$key]->IMAGE_FNAME);
                $data[$key]->PA_IMAGE_AFTERURL = $this->conVBase64($data[$key]->IMAGE_AFTER_PATH.$data[$key]->IMAGE_AFTER_FNAME);
            }
            // $empData = $this->ins->getEmployee($data[$key]->PA_SECTION); เก่า
            $empData = $this->ins->getEmployee($data[$key]->PA_OWNER);
        }
        $res = array(
            'form' => $form,
            'data' => $data,
            'employee' => $empData
        );
        echo json_encode($res);
    }

    public function updateCorrective(){
        $condition = array(
            'NFRMNO' => $_POST['NFRMNO'],
            'VORGNO' => $_POST['VORGNO'],
            'CYEAR'  => $_POST['CYEAR'],
            'CYEAR2' => $_POST['CYEAR2'],
            'NRUNNO' => $_POST['NRUNNO'],
            'CEXTDATA' => '02'
        );
        $empno = $_POST['empCore'];
        $data  = array(
            'VAPVNO' => $empno,
            'VREPNO' => $empno,
        );
        $update = $this->wf->update('FLOW', $data, $condition);
        echo json_encode($update);
    }

    public function updateCorrectiveDetail(){
        $data = json_decode($_POST['data'], true);
        $userno = $_POST['userno'];
        $this->ins->trans_start();
        foreach($data as $key => $d){
            $condition = array(
                'NFRMNO' => $_POST['NFRMNO'],
                'VORGNO' => $_POST['VORGNO'],
                'CYEAR'  => $_POST['CYEAR'],
                'CYEAR2' => $_POST['CYEAR2'],
                'NRUNNO' => $_POST['NRUNNO'],
                'PA_ID'  => $d['PA_ID']
            );
            $dataU = array(
                'PA_EMP_CORRECTIVE' => $d['PA_EMP_CORRECTIVE'],
                'PA_CORRECTIVE'     => $d['PA_CORRECTIVE'],
                'PA_FINISH_DATE'    => $this->conVdateToDB($d['PA_FINISH_DATE']),
                'PA_MORNING_TALK'   => $this->conVdateToDB($d['PA_MORNING_TALK']),
            );
            if(isset($_FILES['PA_IMAGE_AFTER']['name'][$key])){
                $file = array(
                    'name' => $_FILES['PA_IMAGE_AFTER']['name'][$key],
                    'type' => $_FILES['PA_IMAGE_AFTER']['type'][$key],
                    'tmp_name' => $_FILES['PA_IMAGE_AFTER']['tmp_name'][$key],
                    'error' => $_FILES['PA_IMAGE_AFTER']['error'][$key],
                    'size' => $_FILES['PA_IMAGE_AFTER']['size'][$key]
                );
                $upload = $this->uploadfile($file);
                if($upload['status'] == 'success'){
                    $dataU['PA_IMAGE_AFTER'] = $this->setImage($upload, $userno, 'PT');
                }
            }
            $update = $this->ins->update('STY_PATROL', $dataU, $condition);
        }
        $this->ins->trans_complete();
        echo json_encode($update);
    }

    public function updateEvaluate(){
        $data = $_POST['data'];
        foreach($data as $key => $d){
            $condition = array(
                'NFRMNO' => $_POST['NFRMNO'],
                'VORGNO' => $_POST['VORGNO'],
                'CYEAR'  => $_POST['CYEAR'],
                'CYEAR2' => $_POST['CYEAR2'],
                'NRUNNO' => $_POST['NRUNNO'],
                'PA_ID'  => $d['PA_ID']
            );
            $data = array(
                'PA_AUDIT_EVALUATE' => $d['PA_AUDIT_EVALUATE']
            );
            $update = $this->ins->update('STY_PATROL', $data, $condition);
        }
        echo json_encode($update);
    }

    public function inspection($formno){
        $master = $this->wf->getFormMaster('ST-INP');
        $form = array(
            'NFRMNO' => $master[0]->NNO,
            'VORGNO' => $master[0]->VORGNO,
            'CYEAR'  => $master[0]->CYEAR,
            'CYEAR2' => '20'.substr($formno,6,2),
            'NRUNNO' => (int) substr($formno,9,6)
        );
        $empno = '';
        $EmpFlow  = $this->wf->getEmpFlow($form, $empno);
        $approve  = false;
        $cextData = null;
        if(!empty($EmpFlow)){
            $approve = true;
            $cextData = $EmpFlow[0]->CEXTDATA;
        }
        $data['form']    = $form;
        $data['website'] = 'webflow';
        $data['title']   = 'inspection';
        $data['empno']   = $empno;
        $data['approve'] = $approve;
        $data['cextData'] = $cextData;
        // $data['employee'] = $this->ins->getEmployee();
        // echo json_encode($data);
        // $data['patrol']  = $patrol['data'];
        $this->views('inspection/inspectionForm',$data);
    }

}