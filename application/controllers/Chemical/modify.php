<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class modify extends MY_Controller {

    private $title = 'modify-chemical';

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('chemical_model', 'che');
        // $this->load->model('type_model', 'type');
        $this->load->model('Webform_model', 'wf');
        $this->load->model('user_model', 'usr');
    }
    
    public function index(){
        $data['title'] = $this->title;
        $data['page']  = 'modify';
        $data['formInfo'] = $this->wf->getFormMaster('ST-CHM');
        $this->views('chemical/modify',$data);
    }

    public function cancel(){
        $data['title'] = 'cancel-chemical';
        $data['page']  = 'cancel';
        $data['formInfo'] = $this->wf->getFormMaster('ST-CHM');
        $this->views('chemical/modify',$data);
    }

    public function getChemical(){
        $empno    = $this->input->post('empno');
        $userData = $this->usr->getUser($empno);
        $user  = !empty($userData) ? $userData[0] : null;
        $org   = $this->usr->getOwnOrg($empno);
        
        $data = [];
        $orgAll = [];
        $dept = [];
        if(!empty($org)){
            foreach ($org as $key => $o) {
                $pos = $this->getPositionCode($o->VORGNO);
                $dept = array_merge($dept, $this->usr->getOrg(array($pos => $o->VORGNO)));
                // $dept = $this->usr->getOrg(array($pos => $o->VORGNO));
                // if(!empty($dept)){
                //     foreach ($dept as $key => $d) {
                //         // if(strpos(str_replace(' ','',$d->SDEPT),'FEDEPT') !== false || strpos(str_replace(' ','',$d->SDEPT),'ELADEPT') !== false){
                //         //     $orgAll[$key]['name'] = strtoupper($d->SDEPT);
                //         //     $orgAll[$key]['code'] = strtoupper($d->SDEPCODE);
                //         // }else{
                //             $orgAll[$key]['name'] = strtoupper($d->SSEC);
                //             $orgAll[$key]['code'] = strtoupper($d->SSECCODE);
                //         // }
                //     }
                // }
            }
            if(!empty($dept)){
                foreach ($dept as $key => $d) {
                    if(strpos(str_replace(' ','',$d->SDEPT),'FEDEPT') !== false || strpos(str_replace(' ','',$d->SDEPT),'ELADEPT') !== false){
                        $orgAll[$key]['name'] = strtoupper($d->SDEPT);
                        $orgAll[$key]['code'] = strtoupper($d->SDEPCODE);
                    }else{
                        $orgAll[$key]['name'] = strtoupper($d->SSEC);
                        $orgAll[$key]['code'] = strtoupper($d->SSECCODE);
                    }
                }
            }
            $orgAll = array_map("unserialize", array_unique(array_map("serialize", $orgAll)));
            if(!empty($orgAll)){ 
                foreach ($orgAll as $key => $o) {
                    $d = $this->che->getCHMSec(['OWNERCODE' => $o['code'], 'STATUS' => 1]);
                    if(!empty($d)){
                        $data[$o['code']] = $d;
                    }
                }
            }
        }
        $res = array(
            'data' => $data, 
            'user' => $user,
            'org' => $orgAll,
            'dept' => $dept,
            'status' => !empty($data) ? 1 : 0
        );   
        echo json_encode($res);
    }

   
    private function getPositionCode($vorgno) {
        if (substr($vorgno, 2, 2) == '01') {
            return 'SDIVCODE';
        } elseif (substr($vorgno, 4, 2) == '01') {
            return 'SDEPCODE';
        } else {
            return 'SSECCODE';
        }
    }

    public function searchChe(){
        $id   = $this->input->post('id');
        $owner  = $this->input->post('owner');
        $empno  = $this->input->post('empno');
        
        $cond = array(
            'AMEC_SDS_ID' => $id,
            'OWNERCODE'   => $owner
        );
        $data = $this->che->getCHMSec($cond);
        
        if(empty($data)){
            $userData = $this->usr->getUser($empno);
            $user = !empty($userData) ? $userData[0] : null;
            if($user){
                $data = $this->che->getCHMSec(array('AMEC_SDS_ID' => $id, 'OWNERCODE' => $user->SDEPCODE));
            }
        }
        $res = array('data' => $data, 'status' => !empty($data) ? 1 : 0);
        echo json_encode($res);
    }

    public function createForm(){
        try {
            $AMEC_SDS_ID = $this->input->post('AMEC_SDS_ID');
            $userno      = $this->input->post('userno');
            $ownercode   = $this->input->post('ownercode');
            $type        = $this->input->post('type');
            $NFRMNO      = $this->input->post('formtype');
            $VORGNO      = $this->input->post('owner');
            $CYEAR       = $this->input->post('cyear');
            $CYEAR2      = $this->input->post('cyear2');
            $NRUNNO      = $this->input->post('runno');
            // $position    = $this->getPositionCode($ownercode);
            // $owner       = $this->usr->getOrg(array($position => $ownercode));

            $form = array(
                'NFRMNO' => $NFRMNO,
                'VORGNO' => $VORGNO,
                'CYEAR' => $CYEAR,
                'CYEAR2' => $CYEAR2,
                'NRUNNO' => $NRUNNO,
            );
            $data = $this->getdataChm($AMEC_SDS_ID, $ownercode, $form, $userno);

            // $data = array(
            //     'NFRMNO'            => $NFRMNO,
            //     'VORGNO'            => $VORGNO,
            //     'CYEAR'             => $CYEAR,
            //     'CYEAR2'            => $CYEAR2,
            //     'NRUNNO'            => $NRUNNO,
            //     'AMEC_SDS_ID'       => $AMEC_SDS_ID,
            //     'OWNER'             => !empty($this->che->getOwnerByCode($ownercode)) 
            //                            ? $this->che->getOwnerByCode($ownercode)[0]->OWNER 
            //                            : ($owner[0]->SSECCODE == '00' ? $owner[0]->SDEPT : $owner[0]->SSEC),
            //     'OWNERCODE'         => $ownercode,
            //     'USER_CREATE'       => $userno
            // );
            if($type == 'modify'){
                $area = '';
                $keep = '';
                $USED_AREA     = $this->input->post('USED_AREA[]');
                $KEEPING_POINT = $this->input->post('KEEPING_POINT[]');
                foreach($USED_AREA as $key => $a){
                    $area .= $a.'|';
                }
                foreach($KEEPING_POINT as $key => $a){
                    $keep .= $a.'|';
                }
                $data['USED_FOR']      = $this->input->post('USED_FOR');
                $data['USED_AREA']     = rtrim($area,'|');
                $data['KEEPING_POINT'] = rtrim($keep, '|');
                $data['USER_CONTROL']  = $this->input->post('USER_CONTROL');
                $data['FORM_TYPE']     = 2; 
            } else {
                $data['FORM_TYPE']     = 3; 
                $data['REASON_CANCEL'] = $this->input->post('REASON_CANCEL');
            }

            $this->che->trans_start();
            $this->wf->trans_start();
            $this->che->insert('STY_CHEMICAL_FORM', $data);
            
            $this->wf->deleteExtra($form, ['02', '03', '04', '05', '06', '07']);
            $dataWF = ['CSTEPNEXTNO' => '00']; 
            $form['CEXTDATA'] = '01';
            $this->wf->update('FLOW', $dataWF, $form); 
        
            $this->che->trans_complete(); 
            $this->wf->trans_complete(); 
            // if ($this->che->trans_status() === FALSE) {
            //     $status = false;
            // } else {
            //     $status = true;
            // }
            $status = $this->wf->trans_status() === FALSE || $this->che->trans_status() === FALSE ? 0 : 1;
            $res = array(
                'data'   => $data,
                'status' => $status
            );  
            echo json_encode($res);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getdataChm($id, $ownCode, $form, $usr){
        $position  = $this->getPositionCode($ownCode);
        $ownerdata = $this->usr->getOrg(array($position => $ownCode));
        $data = $this->che->getCHMSec(['AMEC_SDS_ID' => $id, 'OWNERCODE' => $ownCode]);
        $res  = array(
            'NFRMNO'            => $form['NFRMNO'],
            'VORGNO'            => $form['VORGNO'],
            'CYEAR'             => $form['CYEAR'],
            'CYEAR2'            => $form['CYEAR2'],
            'NRUNNO'            => $form['NRUNNO'],
            'AMEC_SDS_ID'       => $id,
            'OWNER'             => !empty($owner = $this->che->getOwnerByCode($ownCode)) 
                                   ? $owner[0]->OWNER 
                                   : (($ownerdata[0]->SSECCODE == '00') ? $ownerdata[0]->SDEPT : $ownerdata[0]->SSEC),
            'OWNERCODE'         => $ownCode,
            'RECEIVED_SDS_DATE' => $this->conVdateToDB($data[0]->RECEIVED_SDS_DATE),
            'EFFECTIVE_DATE'    => $this->conVdateToDB($data[0]->EFFECTIVE_DATE),
            'PRODUCT_CODE'      => $data[0]->PRODUCT_CODE,
            'CHEMICAL_NAME'     => $data[0]->CHEMICAL_NAME,
            'REVISION'          => $data[0]->REV,
            'USED_FOR'          => $data[0]->USED_FOR,
            'USED_AREA'         => $data[0]->USED_AREA,
            'KEEPING_POINT'     => $data[0]->KEEPING_POINT,
            'QTY'               => $data[0]->QTY,
            'QUANTITY_KG'       => $data[0]->QUANTITY_KG,
            'QUANTITY_TYPE'     => $data[0]->QUANTITY_TYPE,
            'PUR_CODE'          => $data[0]->PUR_CODE,
            'PUR_INCHARGE'      => $data[0]->PUR_INCHARGE,
            'LAYOUT_FILE'       => $data[0]->LAYOUT_FILE,
            'LAW'               => $data[0]->LAW,
            'HEALTH'            => $data[0]->HEALTH,
            'BEI'               => $data[0]->BEI,
            'ENVIRONMENT'       => $data[0]->ENVIRONMENT,
            'EVM_PARAMETER'     => $data[0]->EVM_PARAMETER,
            'PPE'               => $data[0]->PPE,
            'PPE_EQUIPMENT'     => $data[0]->PPE_EQUIPMENT,
            'SPECIAL_CONTROLLER' => $data[0]->SPECIAL_CONTROLLER,
            'FIRE_EQUIPMENT'    => $data[0]->FIRE_EQUIPMENT,
            'EFC_WASTE'         => $data[0]->EFC_WASTE,
            'EFC_RESULT'        => $data[0]->EFC_RESULT,
            'EFC_REASON'        => $data[0]->EFC_REASON,
            'SDS_FILE'          => $data[0]->SDS_FILE,
            'VENDOR'            => $data[0]->VENDORCODE,
            'VENDOR_NAME'       => $data[0]->VENDOR,
            'VENDOR_ADDRESS'    => $data[0]->VENDOR_ADDRESS,
            'VENDOR_TAX_NO'     => $data[0]->VENDOR_TAX_NO,
            'HAZARDOUS'         => $data[0]->HAZARDOUS,
            'CAS_NO'            => $data[0]->CAS_NO,
            'SUBSTANCE_NAME'    => $data[0]->SUBSTANCE_NAME,
            'SUBSTANCE_WEIGHT'  => $data[0]->SUBSTANCE_WEIGHT,
            'SUBSTANCE_TYPE'    => $data[0]->SUBSTANCE_TYPE,
            'CARCINOGENS'       => $data[0]->CARCINOGENS,
            'CARCINOGENS_DETAIL' => $data[0]->CARCINOGENS_DETAIL,
            'CARCINOGENS_TYPE'   => $data[0]->CARCINOGENS_TYPE,
            'USER_CREATE'        => $usr
        );
        return $res;
    }

   

   

}