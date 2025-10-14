<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_form.php';
// require_once APPPATH.'controllers/_file.php';
require_once APPPATH.'controllers/_safetyForm.php';
class webflow extends MY_Controller {
    
    // use _FILE;
    use _Form;
    use _safetyForm;
    // private $title = 'request-chemical';

    public function __construct(){
		parent::__construct();
        $this->load->model('Webform_model', 'wf');
        $this->load->model('form_model', 'frm');
        $this->load->model('electric_model', 'elec');
        $this->load->model('area_model', 'area');
    }
    
    // public function index($NFRMNO='', $VORGNO='', $CYEAR='', $CYEAR2='', $NRUNNO='', $empno=''){
    public function index($formno = ''){
        if($formno != ''){
            $master = $this->wf->getFormMaster('ST-ECS');
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
        $mode = $this->getMode($form['NFRMNO'], $form['VORGNO'], $form['CYEAR'], $form['CYEAR2'], $form['NRUNNO'], $empno);
        $formData = $this->frm->getForm($form);
        if(!empty($formData)){
            $formData = $formData[0];
            $area = $this->area->getAreas(['AREA_ID' => $formData->FORM_AREA]);
            $formData->AREA_NAME = !empty($area) ? $area[0]->AREA_NAME : '';
        }else{
            $formData = [];
        }
        // $formData = !empty($formData) ? $formData[0] : [];
        $formMaster = $this->frm->getFormMaster(['CATEGORY' => $formData->FORM_CATEGORY, 'FORMNO' => $formData->FORM_NO]);
        $formMaster = !empty($formMaster) ? $formMaster[0] : [];

        $EmpFlow  = $this->wf->getEmpFlow($form, $empno);
        $cstep = '';
        if(!empty($EmpFlow)){
            // $approve  = $EmpFlow[0]->CSTEPST == $this->wf->STEP_READY ? true : false;
            $approve  = true;
            $cstep    = $EmpFlow[0]->CSTEPNO;
            $cextData = $EmpFlow[0]->CEXTDATA;
            // $return   = count($this->wf->checkReturnb($form, $EmpFlow[0]->CSTEPNEXTNO)) > 0 ? true : false;
        }

        $data = array(
            'form'  => $form,
            'formData'    => $formData,
            'formMaster'  => $formMaster,
            'area'  => $area,
            'empno' => $empno,
            'cstep' => $cstep,
            'cextData' => isset($cextData) ? $cextData : null,
            'mode'  => $mode,
            // 'return' => $return
        );
        $this->views('electric/webform',$data);
    }

    public function save(){
        try{
            $post = $this->input->post();
            $data = [];
            $status = false;
            $insert = [];
            foreach($post as $key => $p){
                // echo $key . ' : ' .$p;
                // echo '<br>';

                if(strpos($key,'data') !== false || strpos($key, 'remark') !== false){
                    $k = explode('_',$key);
                    $seq = $k[1];
                }
                
                if(strpos($key,'data') !== false){
                    $topic = str_replace('data','', $k[0]);
                    if(is_array($p)){
                        $data[$seq]['ET_DATA'] = implode('|',$p);
                    }else{
                        $data[$seq]['ET_DATA'] = $p;
                    }
                }else if (strpos($key, 'remark') !== false){
                    $topic = str_replace('remark','', $k[0]);
                    $data[$seq]['ET_REMARK'] = $p;
                }
                $data[$seq]['NFRMNO'] = $post['NFRMNO'];
                $data[$seq]['VORGNO'] = $post['VORGNO'];
                $data[$seq]['CYEAR']  = $post['CYEAR'];
                $data[$seq]['CYEAR2'] = $post['CYEAR2'];
                $data[$seq]['NRUNNO'] = $post['NRUNNO'];
                $data[$seq]['ET_CATEGORY'] = $post['ET_CATEGORY'];
                $data[$seq]['ET_NO'] = $post['ET_NO'];
                $data[$seq]['ET_TOPIC'] = $topic;
                $data[$seq]['ET_SEQ'] = $seq;

            }
            $this->elec->trans_start();
            foreach($data as $key => $d){
                $cond = [
                    'NFRMNO' => $d['NFRMNO'],
                    'VORGNO' => $d['VORGNO'],
                    'CYEAR'  => $d['CYEAR'],
                    'CYEAR2' => $d['CYEAR2'],
                    'NRUNNO' => $d['NRUNNO'],
                    'ET_CATEGORY' => $d['ET_CATEGORY'],
                    'ET_NO'    => $d['ET_NO'],
                    'ET_TOPIC' => $d['ET_TOPIC'],
                    'ET_SEQ'   => $d['ET_SEQ']
                ];
                $check = $this->elec->checkData($cond);
                if(!empty($check)){
                    $this->elec->update('STY_ELECTRIC_INSPECTION', $d, $cond);
                }else{
                    $insert[] = $d;
                }
            }
            if(!empty($insert)){
                $this->elec->insert_batch('STY_ELECTRIC_INSPECTION', $insert);
            }
            // $this->elec->insert_batch('STY_ELECTRIC_INSPECTION', $data);
            $this->elec->trans_complete();
            $status = $this->elec->trans_status() === FALSE  ? 0 : 1;
        } catch (Exception $e) {
            $status = 2;
        } finally {
            $res = [
                'post' => $post,
                'data' => $data,
                'status' => $status
            ];
            echo json_encode($res);
        }
    }

    public function getFormData(){
        $post = $this->input->post();
        $cond = [
            'NFRMNO' => $post['NFRMNO'],
            'VORGNO' => $post['VORGNO'],
            'CYEAR'  => $post['CYEAR'],
            'CYEAR2' => $post['CYEAR2'],
            'NRUNNO' => $post['NRUNNO']
        ];
        $form = $this->elec->getForm($cond);
        echo json_encode($form);
    }

    public function submitAllForm(){
        $post = $this->input->post();
        $form = $this->wf->getFormOnExData($post['NFRMNO'], $post['VORGNO'], $post['CYEAR'], $post['extData']);
        if(empty($form)){
            $res = ['status' => false, 'message' => 'Form not found.'];
        }else{
            $res = ['status' => true, 'message' => 'Form found.', 'form' => $form];
        }
        echo json_encode($res);
    }
}