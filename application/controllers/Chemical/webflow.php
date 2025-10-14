<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_form.php';
require_once APPPATH.'controllers/_file.php';
class webflow extends MY_Controller {
    
    use _FILE;
    use _Form;
    // private $title = 'request-chemical';

    public function __construct(){
		parent::__construct();
        $this->load->model('chemical_model', 'che');
        $this->load->model('Webform_model', 'wf');
        $this->load->model('user_model', 'usr');
        $this->load->model('type_model', 'type');
        $this->upload_path = "//amecnas/AMECWEB/File/" .($this->_servername()=='amecweb' ? 'production' : 'development') ."/safety/files/Chemical/";
    }
    
    // public function index($NFRMNO='', $VORGNO='', $CYEAR='', $CYEAR2='', $NRUNNO='', $empno=''){
    public function index($formno = ''){
        if($formno != ''){
            $master = $this->wf->getFormMaster('ST-CHM');
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
        $cextData = 'view';
        $approve = false;
        $return = false;
        $detail  = null;
        $SDSfile = null;
        if(!empty($EmpFlow)){
            // $approve  = $EmpFlow[0]->CSTEPST == $this->wf->STEP_READY ? true : false;
            $approve  = true;
            $cextData = $EmpFlow[0]->CEXTDATA;
            $return   = count($this->wf->checkReturnb($form, $EmpFlow[0]->CSTEPNEXTNO)) > 0 ? true : false;
        }

        $data = array(
            'form'  => $form,
            'empno'    => $empno,
            'cextData' => $cextData,
            'approve'  => $approve,
            'return'   => $return
        );

        if(!empty($this->che->getFormData($form))){
            $detail  = $this->che->getFormData($form)[0];	
            $request = $this->usr->getUser($detail->USER_CREATE)[0];
            $data['defaultDetail']  = clone $detail;
            $data['detail']  = $this->setFormData($detail);
            $data['request'] = $request;
            if($detail->FORM_TYPE == 1){
                $layoutFile = $this->che->getFile($detail->LAYOUT_FILE)[0];
                if($detail->SDS_FILE != null){
                    $SDSfile = $this->che->getFile($detail->SDS_FILE)[0];
                }
                $data['EFCAPV'] = $this->wf->getApvData($form, '02')[0];
                if($detail->NEW_CHEMICAL == '1'){
                    $data['BPAPV'] = $this->wf->getApvData($form, '04')[0];
                }
                $data['HAZARDOUSCLASS'] = $this->type->getType(array('TYPE_CODE' => 'CMC', 'TYPE_STATUS' => 1));
                $data['CARCINOGENSCLASS'] = $this->type->getType(array('TYPE_CODE' => 'CMCA', 'TYPE_STATUS' => 1));
                $data['layoutFile'] = $layoutFile;
                $data['SDSfile'] = $SDSfile;
                $data['EFC']  = $this->usr->getEFC();
                $data['BP']   = $this->usr->getBP();
                $data['VENDORS'] = $this->getVendor();
                // var_dump($data);
                // echo json_encode($data);
                // exit;
                $this->views('chemical/webform',$data);
            }elseif($detail->FORM_TYPE == 2){
                if(strpos($detail->USER_CONTROL, '|') !== false){
                    $userCon = '';
                    foreach(explode('|', $detail->USER_CONTROL) as $key => $e){
                        $empData = $this->usr->getUser($e);
                        if(!empty($empData)){
                            $userCon .= $empData[0]->SNAME.', ';
                        }
                    }
                    $userCon = rtrim($userCon, ', ');
                    $detail->USER_CONTROL = $userCon;
                } 
                $this->views('chemical/webform',$data);
            }elseif($detail->FORM_TYPE == 3){
                $this->views('chemical/webform',$data);
            }
        }
    }

    private function setFormData($detail) {
        $detail->QUANTITY_TYPE = $detail->QUANTITY_TYPE != null ? ($detail->QUANTITY_TYPE == 1 ? 'kg/วัน' : 'kg/เดือน') : '-';    	

        $detail->LAW		   = $detail->LAW != null ? $detail->LAW : '-'; 	      
        $detail->BEI		   = $detail->BEI != null ? $detail->BEI : ($detail->HEALTH == 0 && $detail->HEALTH != null ? '<span class="text-red-500 ">ไม่จำเป็น</span>' : '-'); 	      
        $detail->EVM_PARAMETER = $detail->EVM_PARAMETER != null ? $detail->EVM_PARAMETER : ($detail->ENVIRONMENT == 0 && $detail->ENVIRONMENT != null ? '<span class="text-red-500 ">ไม่จำเป็น</span>' : '-'); 
        $detail->SPECIAL_CONTROLLER = $detail->SPECIAL_CONTROLLER != null ? ($detail->SPECIAL_CONTROLLER == 1 ? '<span class="text-green-500 ">จำเป็น</span>' : '<span class="text-red-500 ">ไม่จำเป็น</span>') : '-'; 
        $detail->PPE_EQUIPMENT  = $detail->PPE_EQUIPMENT  != null ? str_replace('|',', ',$detail->PPE_EQUIPMENT) : '-';
        $detail->FIRE_EQUIPMENT = $detail->FIRE_EQUIPMENT != null ? str_replace('|',', ',$detail->FIRE_EQUIPMENT) : '-';

        $detail->EFC_WASTE  = $detail->EFC_WASTE  != null ? ($detail->EFC_WASTE == 1 ?   '<span class="text-green-500 ">จำเป็น</span>' : '<span class="text-red-500 ">ไม่จำเป็น</span>') : '-'; 		   
        $detail->EFC_RESULT = $detail->EFC_RESULT != null ? ($detail->EFC_RESULT == 1 ? '<span class="text-green-500 ">ผ่าน</span>' : '<span class="text-red-500 ">ไม่ผ่าน</span>') : '-'; 	          
        $detail->EFC_REASON = $detail->EFC_REASON != null ? $detail->EFC_REASON : '-'; 

        
        $detail->PUR_CODE = $detail->PUR_CODE != null ? '('.$detail->PUR_CODE.')' : '';
        $detail->PUR_INCHARGE = $detail->PUR_INCHARGE != null ? $detail->PUR_INCHARGE : '-';
        $detail->PRODUCT_CODE = $detail->PRODUCT_CODE != null ? $detail->PRODUCT_CODE : '-';
        $detail->VENDOR_NAME  = $detail->VENDOR_NAME != null ? $detail->VENDOR_NAME : '-';
        $detail->VENDOR_TAX_NO  = $detail->VENDOR_TAX_NO != null ? $detail->VENDOR_TAX_NO : '-';
        $detail->VENDOR_ADDRESS = $detail->VENDOR_ADDRESS != null ? $detail->VENDOR_ADDRESS : '-';

        $detail->CLASS = $detail->CLASS != null ? $this->type->getType(array('TYPE_ID' => $detail->CLASS), "TYPE_NAME || ' ' || TYPE_DETAIL AS CLASS")[0]->CLASS : '-';
        
        $detail->HAZARDOUS = $detail->HAZARDOUS != null ? ($detail->HAZARDOUS == 1 ? 'มี' : 'ไม่มี') : '-'; 
        $detail->CAS_NO    = $detail->CAS_NO != null ? $detail->CAS_NO : '-'; 
        $detail->SUBSTANCE_NAME   = $detail->SUBSTANCE_NAME != null ? $detail->SUBSTANCE_NAME : '-'; 
        $detail->SUBSTANCE_WEIGHT = $detail->SUBSTANCE_WEIGHT != null ? $detail->SUBSTANCE_WEIGHT : '-'; 
        $detail->SUBSTANCE_TYPE   = $detail->SUBSTANCE_TYPE != null ? $detail->SUBSTANCE_TYPE : '-'; 
        
        $detail->CARCINOGENS        = $detail->CARCINOGENS != null ? ($detail->CARCINOGENS == 1 ? 'มี' : 'ไม่มี') : '-'; 
        $detail->CARCINOGENS_DETAIL = $detail->CARCINOGENS_DETAIL != null ? $detail->CARCINOGENS_DETAIL : '-'; 
        $detail->CARCINOGENS_TYPE   = $detail->CARCINOGENS_TYPE != null ? $this->type->getType(array('TYPE_ID' => $detail->CARCINOGENS_TYPE), "TYPE_NAME || ' ' || TYPE_DETAIL AS CLASS")[0]->CLASS : '-';
        return $detail; 
    }

    public function safetySave(){
        // $data = $_POST;
        $post = $this->input->post();

        $form = array(
            'NFRMNO' => $post['NFRMNO'],
            'VORGNO' => $post['VORGNO'],
            'CYEAR'  => $post['CYEAR'],
            'CYEAR2' => $post['CYEAR2'],
            'NRUNNO' => $post['NRUNNO']
        );

        $formCond = $form;

        $data = array(
            'LAW'           => $post['LAW'],				   
            'HEALTH'        => $post['HEALTH'],			   
            'BEI'           => $post['BEI'],                
            'ENVIRONMENT'   => $post['ENVIRONMENT'],	   
            'EVM_PARAMETER' => $post['EVM_PARAMETER'],      
            'PPE'           => $post['PPE'],  			   
            'PPE_EQUIPMENT' => $post['PPE_EQUIPMENT'],      
            'SPECIAL_CONTROLLER' => $post['SPECIAL_CONTROLLER'],    
            'FIRE_EQUIPMENT'     => $post['FIRE_EQUIPMENT']  
        );
        $this->wf->trans_start();
        $this->che->trans_start();
        if($post['newChm'] == 1){
            $formCond['CEXTDATA']  = '04';
            $this->wf->update('FLOW', array('VAPVNO' => $post['BP'], 'VREPNO' => $post['BP']), $formCond); 
            $formCond['CEXTDATA']  = '02';
            $this->wf->update('FLOW', array('VAPVNO' => $post['EFC'], 'VREPNO' => $post['EFC']), $formCond); 
            $formCond['CEXTDATA']  = '05';
            $org = $this->usr->getOwnOrg($post['BP']);
            if (!empty($org) && isset($org[0]->VORGNO)) {
                $sem = $this->usr->getSem($org[0]->VORGNO);
                $this->wf->update('FLOW', array('VAPVNO' => $sem[0]->SEMPNO, 'VREPNO' => $sem[0]->SEMPNO), $formCond); 
            }
            $data['EFFECTIVE_DATE'] = $post['EFFECTIVE_DATE'];
        }else{
            $formCond['CEXTDATA'] = '02';
            $this->wf->update('FLOW', array('VAPVNO' => $post['EFC'], 'VREPNO' => $post['EFC']), $formCond); 
        }
        $this->che->update('STY_CHEMICAL_FORM', $data, $form);
        $this->wf->trans_complete();
        $this->che->trans_complete();

        $status = $this->wf->trans_status() === FALSE || $this->che->trans_status() === FALSE ? 0 : 1;
        $res = [ 
            'form' => $form,
            'data' => $data,    
            'status' => $status,
        ];
        echo json_encode($res);
    }

    public function efcSave(){
        $post = $this->input->post();
        $form = array(
            'NFRMNO' => $post['NFRMNO'],
            'VORGNO' => $post['VORGNO'],
            'CYEAR'  => $post['CYEAR'],
            'CYEAR2' => $post['CYEAR2'],
            'NRUNNO' => $post['NRUNNO']
        );

        $formCond = $form;

        $data = array(
            'EFC_WASTE'  => $post['EFC_WASTE'],
            'EFC_RESULT' => $post['EFC_RESULT'],
            'EFC_REASON' => $post['EFC_REASON']
        );
        $chemicalData = null;
        $this->che->trans_start();
        $this->che->update('STY_CHEMICAL_FORM', $data, $form);
        $this->che->trans_complete();

        $status = $this->che->trans_status() === FALSE ? 0 : 1;
        $res = [ 
            'form' => $form,
            'data' => $data,    
            'status' => $status,
            'chemicalData' => $chemicalData,
        ];
        echo json_encode($res);
    }

    public function bpSave(){
        $post = $this->input->post();
        $form = array(
            'NFRMNO' => $post['NFRMNO'],
            'VORGNO' => $post['VORGNO'],
            'CYEAR'  => $post['CYEAR'],
            'CYEAR2' => $post['CYEAR2'],
            'NRUNNO' => $post['NRUNNO']
        );

        $formCond = $form;
        $userno = $post['userno'];
        $data = array(
            'PUR_CODE'     => $post['PUR_CODE'],
            'PUR_INCHARGE' => $post['PUR_INCHARGE'],
            'PRODUCT_CODE' => $post['PRODUCT_CODE'],
            'VENDOR'       => $post['VENDOR'],
            'VENDOR_NAME'  => $post['VENDOR_NAME'],
            'VENDOR_TAX_NO'   => $post['VENDOR_TAX_NO'],
            'VENDOR_ADDRESS'  => $post['VENDOR_ADDRESS'],
        );
        $chemicalData = null;
        $this->che->trans_start();
        if(isset($_FILES['SDS_FILE'])){
            $file = $this->uploadfile($_FILES['SDS_FILE']);
            if($file['status'] == 'success'){
                $data['SDS_FILE'] = $this->setFile($file, $userno, 'CM');
            }
        }
        $this->che->update('STY_CHEMICAL_FORM', $data, $form);
        $this->che->trans_complete();

        $status = $this->che->trans_status() === FALSE ? 0 : 1;
        $res = [ 
            'form' => $form,
            'data' => $data,    
            'status' => $status,
            'chemicalData' => $chemicalData,
        ];
        echo json_encode($res);
    }

    public function safetySave2(){
        $post = $this->input->post();
        $form = array(
            'NFRMNO' => $post['NFRMNO'],
            'VORGNO' => $post['VORGNO'],
            'CYEAR'  => $post['CYEAR'],
            'CYEAR2' => $post['CYEAR2'],
            'NRUNNO' => $post['NRUNNO']
        );

        $formCond = $form;
        $data = array(
            'CLASS'              => $post['CLASS'],
            'HAZARDOUS'          => $post['HAZARDOUS'],
            'CARCINOGENS'        => $post['CARCINOGENS'],
            'CAS_NO'             => $post['CAS_NO'],
            'SUBSTANCE_NAME'     => $post['SUBSTANCE_NAME'],
            'SUBSTANCE_WEIGHT'   => $post['SUBSTANCE_WEIGHT'],
            'SUBSTANCE_TYPE'     => $post['SUBSTANCE_TYPE'],
            'CARCINOGENS_DETAIL' => $post['CARCINOGENS_DETAIL'],
            'CARCINOGENS_TYPE'   => $post['CARCINOGENS_TYPE'],
        );

        $this->che->trans_start();
        $this->che->update('STY_CHEMICAL_FORM', $data, $form);
        $this->che->trans_complete();
        $status = $this->che->trans_status() === FALSE ? 0 : 1;
        $res = [ 
            'form' => $form,
            'data' => $data,    
            'status' => $status,
        ];
        echo json_encode($res);
    }

    public function finalSave(){
        $form = array(
            'NFRMNO' => $this->input->post('NFRMNO'),
            'VORGNO' => $this->input->post('VORGNO'),
            'CYEAR'  => $this->input->post('CYEAR'),
            'CYEAR2' => $this->input->post('CYEAR2'),
            'NRUNNO' => $this->input->post('NRUNNO'),
        );
        $apr = $this->input->post('apr');

        $this->che->trans_start();

        $this->che->update('STY_CHEMICAL_FORM', ['RECEIVED_SDS_DATE' => 'sysdate'], $form);
        // $formData = $this->che->getFormData($form);

        $formData = $this->setDataMaster($form);
        // var_dump($formData);
        if(!empty($formData)){
            $this->setDataSecMaster($apr, $formData);
            $this->setDataSec($formData);
            $this->setDataDetail($formData);
            $chemicalData = $this->che->getChemical(['AMEC_SDS_ID' => $formData[0]->AMEC_SDS_ID]);
            if(!empty($chemicalData)){
                // $this->che->update('STY_CHEMICAL_FORM',['REVISION' => $chemicalData[0]->REV], ['AMEC_SDS_ID' => $chemicalData[0]->AMEC_SDS_ID]);
                $this->che->update('STY_CHEMICAL_FORM',['REVISION' => $chemicalData[0]->REV], $form);
            }
        }
        // $this->setDataSecMaster($apr, $formData);
        // $this->setDataSec($formData);
        // $this->setDataDetail($formData);
        $this->che->trans_complete();

        $res = [
            'status' => $this->che->trans_status() === FALSE ? 0 : 1,
            'form'   => $form,
        ];
        echo json_encode($res);
    }

    private function setDataMaster($form){
        $data = $this->che->getFormData($form);
        // var_dump($data);
        if (!empty($data)) {
            foreach ($data as $d) {
                $dataMaster = [
                    'AMEC_SDS_ID'       => $this->che->generate_id('STY_CHEMICAL','AMEC_SDS_ID'),
                    'RECEIVED_SDS_DATE' => $this->conVdateToDB($d->RECEIVED_SDS_DATE),
                    'EFFECTIVE_DATE'    => $this->conVdateToDB($d->EFFECTIVE_DATE),
                    'PRODUCT_CODE'      => $d->PRODUCT_CODE,
                    'CHEMICAL_NAME'     => $d->CHEMICAL_NAME,
                    'PUR_INCHARGE'      => $d->PUR_INCHARGE,
                    'VENDOR'            => $d->VENDOR_NAME,
                    'UN_CLASS'          => !empty($d->CLASS) ? $this->type->getType(array('TYPE_ID' => $d->CLASS))[0]->TYPE_NO : null,
                    'REV'               => '*',
                    'USER_CREATE'       => $d->USER_CREATE,
                    'CREATE_DATE'       => 'sysdate',

                ];
                // $cond = [
                //     'AMEC_SDS_ID' => $d->AMEC_SDS_ID
                // ];
                // $check = $this->che->checkMaster($cond);
                // if (empty($check)) {
                if ($d->AMEC_SDS_ID == null) {      
                    $this->che->insert('STY_CHEMICAL', $dataMaster);
                    $this->che->update('STY_CHEMICAL_FORM', ['AMEC_SDS_ID' => $dataMaster['AMEC_SDS_ID']], $form);
                }
            }
            return $this->che->getFormData($form);
        }else{
            return $data;
        }
    }

    /**
     * Update or Insert STY_CHEMICAL_REV
     * @param string $apr e.g. 95025
     * @param array $data
     */
    private function setDataSecMaster($apr, $data){
        if(!empty($data)){
            foreach ($data as $d) {
                $dataSec = [
                    'USER_APPROVE' => $apr,
                    'APPROVE_DATE' => 'sysdate',
                    'REV' => $this->runRevision($this->che->checkMasterSec(['OWNER' => 'MASTER'])[0]->REV)
                ];
                $this->che->update('STY_CHEMICAL_REV', $dataSec, ['OWNER' => 'MASTER']);

                $cond = ['OWNERCODE' => $d->OWNERCODE];
                $check = $this->che->checkMasterSec($cond);
                // var_dump($check);

                if (empty($check)) {
                    $dataSec['OWNER']       = $d->OWNER;
                    $dataSec['OWNERCODE']   = $d->OWNERCODE;
                    $dataSec['REV']         = '*';
                    $dataSec['TYPE']        = $this->type->getTypeByCode('CMS')[0]->TYPE_ID;
                    $dataSec['USER_CREATE'] = $d->USER_CREATE;
                    $dataSec['CREATE_DATE'] = 'sysdate';
                    $this->che->insert('STY_CHEMICAL_REV',$dataSec);
                }else{
                    $dataSec['USER_UPDATE'] = $d->USER_CREATE;
                    $dataSec['UPDATE_DATE'] = 'sysdate';
                    $dataSec['REV']         = $this->runRevision($check[0]->REV);
                    $this->che->update('STY_CHEMICAL_REV', $dataSec, ['OWNERCODE' => $d->OWNERCODE]);
                }
            }
        }
    }

    private function setDataSec($data){
        if(!empty($data)){
            foreach($data as $d){
                $dataSec = [
                    'RECEIVED_SDS_DATE' => $this->conVdateToDB($d->RECEIVED_SDS_DATE),
                    'USED_FOR'      => $d->USED_FOR,
                    'USED_AREA'     => $d->USED_AREA,
                    'KEEPING_POINT' => $d->KEEPING_POINT,
                    'QTY'     => $d->QTY,
                    'REC4052' => 1,
                    'REC4054' => 1,
                    'STATUS'  => 1
                ];
                $cond = [
                    'OWNERCODE'   => $d->OWNERCODE,
                    'AMEC_SDS_ID' => $d->AMEC_SDS_ID
                ];
                if (empty($this->che->checkDataSec($cond))) {
                    $dataSec['AMEC_SDS_ID'] = $d->AMEC_SDS_ID;
                    $dataSec['OWNER']       = $d->OWNER;
                    $dataSec['OWNERCODE']   = $d->OWNERCODE;
                    $dataSec['USER_CREATE'] = $d->USER_CREATE;
                    $dataSec['CREATE_DATE'] = 'sysdate';
                    $this->che->insert('STY_CHEMICAL_SECTION', $dataSec);
                } else {
                    $dataSec['USER_UPDATE'] = $d->USER_CREATE;
                    $dataSec['UPDATE_DATE'] = 'sysdate';
                    $this->che->update('STY_CHEMICAL_SECTION', $dataSec, $cond);
                }
            }
        }
    }

    private function setDataDetail($data){
        if(!empty($data)){
            foreach($data as $d){
                $dataDetail = [
                    // 'OWNER'         => $d->OWNER,
                    // 'OWNERCODE'     => $d->OWNERCODE,
                    // 'AMEC_SDS_ID'   => $d->AMEC_SDS_ID,
                    'QUANTITY_KG'   => $d->QUANTITY_KG,
                    'QUANTITY_TYPE' => $d->QUANTITY_TYPE,
                    'LAYOUT_FILE'   => $d->LAYOUT_FILE,
                    'LAW'           => $d->LAW,
                    'HEALTH'        => $d->HEALTH,
                    'BEI'           => $d->BEI,
                    'ENVIRONMENT'   => $d->ENVIRONMENT,
                    'EVM_PARAMETER' => $d->EVM_PARAMETER,
                    'PPE'           => $d->PPE,
                    'PPE_EQUIPMENT' => $d->PPE_EQUIPMENT,
                    'SPECIAL_CONTROLLER' => $d->SPECIAL_CONTROLLER,
                    'FIRE_EQUIPMENT'     => $d->FIRE_EQUIPMENT,
                    'EFC_WASTE'  => $d->EFC_WASTE,
                    'EFC_RESULT' => $d->EFC_RESULT,
                    'EFC_REASON' => $d->EFC_REASON,
                    'PUR_CODE'     => $d->PUR_CODE,
                    'SDS_FILE'     => $d->SDS_FILE,
                    'VENDOR'       => $d->VENDOR,
                    'VENDOR_ADDRESS'  => $d->VENDOR_ADDRESS,
                    'VENDOR_TAX_NO'   => $d->VENDOR_TAX_NO,
                    'HAZARDOUS'          => $d->HAZARDOUS,
                    'CAS_NO'             => $d->CAS_NO,
                    'SUBSTANCE_NAME'     => $d->SUBSTANCE_NAME,
                    'SUBSTANCE_WEIGHT'   => $d->SUBSTANCE_WEIGHT,
                    'SUBSTANCE_TYPE'     => $d->SUBSTANCE_TYPE,
                    'CARCINOGENS'        => $d->CARCINOGENS,
                    'CARCINOGENS_DETAIL' => $d->CARCINOGENS_DETAIL,
                    'CARCINOGENS_TYPE'   => $d->CARCINOGENS_TYPE,
                ];
                $cond = [
                    'OWNERCODE'   => $d->OWNERCODE,
                    'AMEC_SDS_ID' => $d->AMEC_SDS_ID
                ];
                if (empty($this->che->checkDataDetail($cond))) {
                    $dataDetail['AMEC_SDS_ID'] = $d->AMEC_SDS_ID;
                    $dataDetail['OWNER']       = $d->OWNER;
                    $dataDetail['OWNERCODE']   = $d->OWNERCODE;
                    $this->che->insert('STY_CHEMICAL_SECDETAIL', $dataDetail);
                } else {
                    $this->che->update('STY_CHEMICAL_SECDETAIL', $dataDetail, $cond);
                }
            }
        }
    }

    public function saveEdit(){
        $form = array(
            'NFRMNO' => $this->input->post('NFRMNO'),
            'VORGNO' => $this->input->post('VORGNO'),
            'CYEAR'  => $this->input->post('CYEAR'),
            'CYEAR2' => $this->input->post('CYEAR2'),
            'NRUNNO' => $this->input->post('NRUNNO'),
        );
        $empno = $this->input->post('userno');
        $data  = $this->che->getFormData($form);
        if (!empty($data)) {
            foreach ($data as $d) {
                $dataSec = [
                    'USED_FOR'      => $d->USED_FOR,
                    'USED_AREA'     => $d->USED_AREA,
                    'KEEPING_POINT' => $d->KEEPING_POINT,
                    'USER_UPDATE'   => $empno,
                    'UPDATE_DATE'   => 'sysdate'   
                ];
                $cond = [
                    'AMEC_SDS_ID' => $d->AMEC_SDS_ID,
                    'OWNERCODE'  => $d->OWNERCODE
                ];
                // $userUpdate = [
                //     'USER_UPDATE' => $empno,
                //     'UPDATE_DATE' => 'sysdate',
                //     'REV' => $this->runRevision($this->che->checkMasterSec(['OWNER' => 'MASTER'])[0]->REV)
                // ];
                $this->che->trans_start();
                $this->che->update('STY_CHEMICAL_SECTION', $dataSec, $cond);
                $this->updateRevision($empno, $d->OWNERCODE, $d->USER_CONTROL);
                // $this->che->update('STY_CHEMICAL_REV', $userUpdate, ['OWNER' => 'MASTER']);
                // $cond = ['OWNERCODE' => $d->OWNERCODE];
                // $userUpdate['REV'] = $this->runRevision($this->che->checkMasterSec($cond)[0]->REV);
                // $userUpdate['USER_CONTROL'] = $d->USER_CONTROL;
                // $this->che->update('STY_CHEMICAL_REV', $userUpdate, $cond);
                $chemicalData = $this->che->getChemical(['AMEC_SDS_ID' => $d->AMEC_SDS_ID]);
                if(!empty($chemicalData)){
                    $this->che->update('STY_CHEMICAL_FORM',['REVISION' => $chemicalData[0]->REV], $form);
                }
                $this->che->trans_complete(); 
            }
        }
        $status = $this->che->trans_status() === FALSE ? 0 : 1;
        $res = [
            'status' => $status
        ];  
        echo json_encode($res);
    }

    private function updateRevision($empno, $ownercode, $userControl=''){
        $data = [
            'USER_UPDATE' => $empno,
            'UPDATE_DATE' => 'sysdate',
            'REV' => $this->runRevision($this->che->checkMasterSec(['OWNER' => 'MASTER'])[0]->REV)
        ];
        $this->che->update('STY_CHEMICAL_REV', $data, ['OWNER' => 'MASTER']);

        if ($userControl != '') {
            $data['USER_CONTROL'] = $userControl;
        }
        $data['REV'] = $this->runRevision($this->che->checkMasterSec(['OWNERCODE' => $ownercode])[0]->REV);
        $this->che->update('STY_CHEMICAL_REV', $data, ['OWNERCODE' => $ownercode]);
    }

    public function saveCancel(){
        $form = array(
            'NFRMNO' => $this->input->post('NFRMNO'),
            'VORGNO' => $this->input->post('VORGNO'),
            'CYEAR'  => $this->input->post('CYEAR'),
            'CYEAR2' => $this->input->post('CYEAR2'),
            'NRUNNO' => $this->input->post('NRUNNO'),
        );
        $empno = $this->input->post('userno');
        $data  = $this->che->getFormData($form);
        if (!empty($data)) {
            foreach($data as $d){
                $dataSec = [
                    'STATUS'      => 0,
                    'USER_UPDATE' => $empno,
                    'UPDATE_DATE' => 'sysdate'
                ];
                $cond = [
                    'AMEC_SDS_ID' => $d->AMEC_SDS_ID,
                    'OWNERCODE'   => $d->OWNERCODE
                ];
            }
            $this->che->trans_start();
            $this->che->update('STY_CHEMICAL_SECTION', $dataSec, $cond);
            $this->updateRevision($empno, $d->OWNERCODE);
            $this->che->trans_complete(); 
        }
        $status = $this->che->trans_status() === FALSE ? 0 : 1;
        $res = [
            'status' => $status
        ];  
        echo json_encode($res);
    }

    /**
     * Run revision
     * @param string $rev e.g. A B C AB AZZ
     */
    private function runRevision($rev){
        if($rev == '*') {
            return 'A';
        } else {
            return $this->nextLetter($rev);
        }
    }

    /**
     * Get next letter
     * @param string $input e.g. A B C AB AZZ
     */
    private function nextLetter($input) {
        $length = strlen($input);
        $result = '';
        $carry = true; // เริ่มต้นให้มีการทบเลข
        for ($i = $length - 1; $i >= 0; $i--) {
            $char = $input[$i];
            if ($carry) {
                if ($char === 'Z') {
                    $result = 'A' . $result;
                } else {
                    $result = chr(ord($char) + 1) . $result;
                    $carry = false; // ถ้าไม่ใช่ 'Z' แล้ว ก็หยุดทบเลข
                }
            } else {
                $result = $char . $result;
            }
        }
    
        // ถ้าตัวอักษรทั้งหมดเป็น 'Z' (เช่น 'Z', 'ZZ') ให้เติม 'A' ด้านหน้า
        if ($carry) {
            $result = 'A' . $result;
        }
        return  $result;
    }

    public function getVendor(){
        if ($this->isAjaxRequest()) {
            echo json_encode($this->usr->getVendor());
        } else {
            $vendor = $this->usr->getVendor();
            $uniqueVendor = [];
            if (!empty($vendor)) {
                foreach ($vendor as $v) {
                    if (!array_key_exists($v->VENCODE, $uniqueVendor)) {
                        $uniqueVendor[$v->VENCODE] = $v;
                    }
                }
                $uniqueVendor = array_values($uniqueVendor);
            }
            return $uniqueVendor;
        }

    }

    public function getUser(){
        $empno = $this->input->post('empno');
        echo json_encode($this->usr->getUser($empno));
    }

    public function getClass(){
        $code = $this->input->post('code');
        echo json_encode($this->type->getType(array('TYPE_CODE' => $code, 'TYPE_STATUS' => 1)));
    }

}