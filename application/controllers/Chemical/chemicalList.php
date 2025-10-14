<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class chemicalList extends MY_Controller {

    private $title = 'chemical-list';

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('chemical_model', 'che');
        $this->load->model('type_model', 'type');
        $this->load->model('user_model', 'usr');
    }
    
    public function index(){
        $data['title'] = $this->title;
        $data['class'] = $this->type->getType(array('TYPE_CODE' => 'CMC', 'TYPE_STATUS' => 1));
        $this->views('chemical/list',$data);
    }

    public function section(){
        $data['title'] = 'Chemical-section';
        $data['rev'] = $this->che->getRev(['OWNERCODE !='=> '999999']);
        $data['class'] = $this->type->getType(array('TYPE_CODE' => 'CMC', 'TYPE_STATUS' => 1));
        $this->views('chemical/sectionList',$data);
    }

    public function getChmAllSec(){
        $res['data'] = [];
        $res['status'] = 0;
        $res['rev'] = $this->getRev();
        $res['sec'] = $this->che->getCheAllSec();
        $res['userControl'] = $this->getUserControl();

        
        // var_dump($rev);
        if(!empty($rev = $this->che->getRev())){
            foreach($rev as $r){
                $chm = $this->che->getChemicalSec($r->OWNERCODE);
                if(!empty($chm)){
                    $res['data'][$r->OWNER] = $chm;
                    $res['status'] = 1;
                }
            }
        }
        echo json_encode($res);
    }
    public function getChmSec(){
        $OWNERCODE = $this->input->post('OWNERCODE');
        $OWNER = $this->input->post('OWNER');
        $res['data'] = [];
        $res['status'] = 0;
        $res['rev'] = $this->getRev();
        $res['userControl'] = $this->getUserControl();
        
        
        $chm = $this->che->getChemicalSec($OWNERCODE);
        if(!empty($chm)){
            $res['data'][strtoupper($OWNER)] = $chm;
            $res['status'] = 1;
        }
            
        echo json_encode($res);
    }

    /**
     * Get all chemicals.
     */
    // public function getOwnOrg($empno){
    public function getOwnOrg(){
        $empno = $_POST['empno'];
        $group = $_POST['group'];
        $org   = $this->usr->getOwnOrg($empno);
        $sec   = $this->che->getCheAllSec();
        $res   = array();
        $res['status'] = 0;
        $res['sec'] = $sec;
        if($group == 'ADM' || $group == 'DEV'){
        // if($group == 'ADM' ){
            $data = $this->che->getMaster($sec);
            if(!empty($data)){
                $res['data']['Master'] = $data;
                $res['status'] = 1;
            }
        }else{
            if(!empty($org)){
                $orgAll = [];
                $data = [];
                foreach ($org as $key => $o) {
                    $pos  = substr($o->VORGNO,2,2) == '01' ? 'SDIVCODE' : 
                            (substr($o->VORGNO,4,2) == '01' ? 'SDEPCODE' : 'SSECCODE');
                    $dept = $this->usr->getOrg(array($pos => $o->VORGNO));
                    // var_dump($dept) ;
                    if(!empty($dept)){
                        foreach ($dept as $key => $d) {
                            if($pos == 'SDIVCODE'){
                                $orgAll['name'][] = strtoupper($d->SDIV);
                                $orgAll['code'][] = strtoupper($d->SDIVCODE);
                            }elseif($pos == 'SDEPCODE'){
                                $orgAll['name'][] = strtoupper($d->SDEPT);
                                $orgAll['code'][] = strtoupper($d->SDEPCODE);
                            }
                            $orgAll['name'][] = strtoupper($d->SSEC);
                            $orgAll['code'][] = strtoupper($d->SSECCODE);
                        }
                    }
                }
                $orgAll['name'] = array_unique($orgAll['name']);
                $orgAll['code'] = array_unique($orgAll['code']);
                // var_dump($orgAll);
                $res['orgAll'] = $orgAll;
                if(!empty($orgAll)){
                    foreach ($orgAll['code'] as $key => $o) {
                        // var_dump($o);
                        // $data = $this->che->getChemicalSec(str_replace(' ','',$o));
                        $data = $this->che->getChemicalSec($o);
                        if(!empty($data)){
                            $res['data'][$orgAll['name'][$key]] = $data;
                            $res['status'] = 1;
                        }
                    }
                }
                // สำหรับที่เป็น dept เมื่อ sec ไม่มีข้อมูล เช่น FE DEPT
                if(empty($data)){
                    if(!empty($user = $this->usr->getUser($empno)[0])){
                        // var_dump($user);
                        // $data = $this->che->getChemicalSec(str_replace(' ','',$user->SDEPT));
                        $data = $this->che->getChemicalSec($user->SDEPCODE);
                        if(!empty($data)){
                            $res['data'][$user->SDEPT] = $data;
                            $res['status'] = 1;
                        }
                    }
                    
                }
            }
        }
        $res['rev'] = $this->getRev();
        $res['org'] =  $org;
        $res['userControl'] = $this->getUserControl();
        echo json_encode($res);
    }

    public function updateRev(){
        $status = 0;
        $rev    = strtoupper($_POST['rev']);
        $own    = $_POST['own'];
        $userno = $_POST['USER_UPDATE'];
        $condition = array(
            'OWNER' => $own,
            'TYPE'  => $this->che->customSelect('STY_TYPE',array('TYPE_CODE' => 'CM'),'TYPE_ID')[0]->TYPE_ID,
            'REV'   => $rev
        );
        $data = array(
            'REV' => $rev,
            'UPDATE_DATE' => 'sysdate',
            'USER_UPDATE' => $userno
        );

        if(count($this->che->customSelect('STY_CHEMICAL_REV', $condition)) == 0){
            unset($condition['REV']);
            $status = $this->che->update('STY_CHEMICAL_REV', $data, $condition);
        }else{
            $status = 3;
        }
        $res = array(
            'status' => $status,
            'rev'    => $this->getRev()
        );
        echo json_encode($res);
    }

    private function getRev(){
        $rev = $this->che->getRev();
        $res = [];
        foreach($rev as $r){
            $res[strtoupper($r->OWNER)] = $r->REV;
        }
        return $res;
    }
    private function getUserControl(){
        $rev = $this->che->getRev();
        $res = [];
        foreach($rev as $r){
            if(strpos($r->USER_CONTROL,'|') !== false){
                $users = explode('|',$r->USER_CONTROL);
                $userControl = '';
                foreach($users as $key => $user){
                    $userData = $this->usr->getUser($user);

                    $userControl .= ($key+1).'. '.substr($userData[0]->STNAME, 0, strpos($userData[0]->STNAME, ' ')).' ('.$userData[0]->SEMPPRE.substr($userData[0]->SNAME, 0, strpos($userData[0]->SNAME, ' ')).')|';
                }
                $res[strtoupper($r->OWNER)] = rtrim($userControl, '|');
            } elseif($r->USER_CONTROL != null) {
                $userData = $this->usr->getUser($r->USER_CONTROL);
                $res[strtoupper($r->OWNER)] = '1. '.substr($userData[0]->STNAME, 0, strpos($userData[0]->STNAME, ' ')).' ('.$userData[0]->SEMPPRE.substr($userData[0]->SNAME, 0, strpos($userData[0]->SNAME, ' ')).')';
            }
        }
        return $res;
    }

    public function save(){
        $id     = $_POST['AMEC_SDS_ID'];
        $userno = $_POST['USER_UPDATE'];
        $data = array(
            'RECEIVED_SDS_DATE' => $_POST['RECEIVED_SDS_DATE'],
            'EFFECTIVE_DATE'    => $_POST['EFFECTIVE_DATE'],
            'PRODUCT_CODE'      => $_POST['PRODUCT_CODE'],
            'VENDOR'            => $_POST['VENDOR'],
            'PUR_INCHARGE'      => $_POST['PUR_INCHARGE'],
            'CHEMICAL_NAME'     => $_POST['CHEMICAL_NAME'],
            'UN_CLASS'          => $_POST['UN_CLASS'],
            'REV'               => $_POST['REV']
        );
        if(!empty($id)){
            $data['UPDATE_DATE'] = 'sysdate';
            $data['USER_UPDATE'] = $userno;
            $res = $this->che->update('STY_CHEMICAL', $data, array('AMEC_SDS_ID' => $id));
        }else{
            $data['AMEC_SDS_ID'] = $this->che->generate_id('STY_CHEMICAL','AMEC_SDS_ID');
            $data['USER_CREATE'] = $userno;
            $res = $this->che->insert('STY_CHEMICAL',$data);
        }
        $sec = $this->che->getCheAllSec();
        $res = array(
            'data'   => array('Master' => $this->che->getMaster($sec)),
            'status' => $res,
            'sec'    => $sec
        );
        echo json_encode($res);
    }

    public function del(){
        $id = $_POST['id'];
        $this->che->trans_start();
        $upSec = $this->che->update('STY_CHEMICAL_SECTION', array('STATUS' => 0), array('AMEC_SDS_ID' => $id));
        $up    = $this->che->update('STY_CHEMICAL', array('STATUS' => 0), array('AMEC_SDS_ID' => $id));
        $sec   = $this->che->getCheAllSec();
        $this->che->trans_complete();

        $res = array(
            'data'   => array('Master' => $this->che->getMaster($sec)),
            'status' => $upSec && $up,
            'sec'    => $sec
        );
        echo json_encode($res);
    }

    public function getOld(){
        $sec = $this->che->getCheAllSec();
        echo json_encode($this->che->getMaster($sec,0));
    }

    public function getSecRebuild(){
        $id   = json_decode($_POST['id'], true);
        $sec  = json_decode($_POST['sec'], true);
        $data = $this->che->getSecRebuild(implode(',',$id), $sec);
        echo json_encode($data);
    }

    public function statusOn(){
        $data  = json_decode($_POST['data'], true);
        // $sec   = json_decode($_POST['sec'], true);
        $testM = array();
        $test  = array();
        $status = 0;
        foreach($data as $d){
            $dUp = array('STATUS' => 1);
            // var_dump($d);
            // var_dump($d['AMEC_SDS_ID']);
            $conUpM = array('AMEC_SDS_ID' => $d['AMEC_SDS_ID']);
            $this->che->trans_start();
            foreach($d as $key => $val){
                if((strpos($key, 'SEC') !== false || strpos($key, 'DEPT') !== false || strpos($key, 'Sec') !== false) && $val == 'Y'){
                    // $conUp['OWNER'] = $key.'.';
                    $conUpS = array(
                        'AMEC_SDS_ID' => $d['AMEC_SDS_ID'],
                        'OWNER'       => $key.'.'
                    );
                    // $test['test'][] = $conUpS;
                    $status = $this->che->update('STY_CHEMICAL_SECTION', $dUp, $conUpS);
                }
            }
            $status = $this->che->update('STY_CHEMICAL', $dUp, $conUpM);
            $this->che->trans_complete();
            // $testM['testM'][] = $conUp;
        }
        // $id = $_POST['id'];
        // $res = $this->che->update('STY_CHEMICAL', array('STATUS' => 1), array('AMEC_SDS_ID' => $id));
        // $sec = $this->che->getCheAllSec();
        $res = array(
            'data'   => $data,
            'status' => $status,
        );
        echo json_encode($res);
    }

    public function getDataForPDF(){
        $owner = $this->input->post('owner');
        $dataStamp = empty($this->che->getDataStamp($owner)) ? $this->usr->getSTManager() : $this->che->getDataStamp($owner);
        if(!empty($dataStamp)){
            foreach ($dataStamp as $stamp) {
                $stamp->aprDate = empty($stamp->APPROVE_DATE) ? date('j M Y') : date('j M Y', strtotime($stamp->APPROVE_DATE));
            }
            // $dataStamp[0]->aprDate = date('j M Y', strtotime($dataStamp[0]->APPROVE_DATE));
        }
        // else  {
        //     // $dataStamp = $this->usr->getSTManager();
        //     $dataStamp[0]->aprDate = date('j M Y');
        //     // if (!empty($dataStamp)) {
        //         // $dataStamp[0]->aprDate = date('j M Y');
        //     // }
        // }

        $res = array(
            'sec'     => $this->che->getCheAllSec('*'),
            'class'   => $this->type->getType(array('TYPE_CODE' => 'CMC', 'TYPE_STATUS' => 1)),   
            // 'manager' => !empty($dataStamp) ? $dataStamp : $this->usr->getSTManager(),
            'manager' => $dataStamp,
            'owner'   => $owner,
            'dataStamp' => $dataStamp,
            'userControl' => $this->getUserControl()
        );
        echo json_encode($res);
    }

    public function getDetail(){
        $OWNERCODE = $this->input->post('OWNERCODE');
        $id        = $this->input->post('id');
        $data = $this->che->getCHMSec(['OWNERCODE' => $OWNERCODE, 'AMEC_SDS_ID' => $id]);
        $res['data'] = $data;
        if(!empty($data)){
            foreach($data as $key => $detail) {
                $detailCopy = clone $detail;
                $res['detail'][] = $this->setFormData($detailCopy);
            }
        }
        echo json_encode($res);
    }

    private function setFormData($detail) {
        $detail->QUANTITY_KG = $detail->QUANTITY_KG != null ? $detail->QUANTITY_KG  : '';    	
        $detail->QUANTITY_TYPE = $detail->QUANTITY_TYPE != null ? ($detail->QUANTITY_TYPE == 1 ? 'kg/วัน' : 'kg/เดือน') : '-';    	

        $detail->LAW		   = $detail->LAW != null ? $detail->LAW : '-'; 	      
        // $detail->BEI		   = $detail->BEI != null ? $detail->BEI : ($detail->HEALTH == 0 && $detail->HEALTH != null ? '<span class="text-red-500 ">ไม่จำเป็น</span>' : '-'); 	      
        $detail->BEI		   = $detail->BEI != null ? $detail->BEI : ($detail->HEALTH == 0 && $detail->HEALTH != null ? 'ไม่จำเป็น' : '-'); 	      
        // $detail->EVM_PARAMETER = $detail->EVM_PARAMETER != null ? $detail->EVM_PARAMETER : ($detail->ENVIRONMENT == 0 && $detail->ENVIRONMENT != null ? '<span class="text-red-500 ">ไม่จำเป็น</span>' : '-'); 
        $detail->EVM_PARAMETER = $detail->EVM_PARAMETER != null ? $detail->EVM_PARAMETER : ($detail->ENVIRONMENT == 0 && $detail->ENVIRONMENT != null ? 'ไม่จำเป็น' : '-'); 
        // $detail->SPECIAL_CONTROLLER = $detail->SPECIAL_CONTROLLER != null ? ($detail->SPECIAL_CONTROLLER == 1 ? '<span class="text-green-500 ">จำเป็น</span>' : '<span class="text-red-500 ">ไม่จำเป็น</span>') : '-'; 
        $detail->SPECIAL_CONTROLLER = $detail->SPECIAL_CONTROLLER != null ? ($detail->SPECIAL_CONTROLLER == 1 ? 'จำเป็น' : 'ไม่จำเป็น') : '-'; 
        $detail->PPE_EQUIPMENT  = $detail->PPE_EQUIPMENT  != null ? str_replace('|',', ',$detail->PPE_EQUIPMENT) : '-';
        $detail->FIRE_EQUIPMENT = $detail->FIRE_EQUIPMENT != null ? str_replace('|',', ',$detail->FIRE_EQUIPMENT) : '-';

        $detail->EFC_WASTE  = $detail->EFC_WASTE  != null ? ($detail->EFC_WASTE == 1 ?   'จำเป็น' : 'ไม่จำเป็น') : '-'; 		   
        $detail->EFC_RESULT = $detail->EFC_RESULT != null ? ($detail->EFC_RESULT == 1 ? 'ผ่าน' : 'ไม่ผ่าน') : '-'; 	          
        // $detail->EFC_WASTE  = $detail->EFC_WASTE  != null ? ($detail->EFC_WASTE == 1 ?   '<span class="text-green-500 ">จำเป็น</span>' : '<span class="text-red-500 ">ไม่จำเป็น</span>') : '-'; 		   
        // $detail->EFC_RESULT = $detail->EFC_RESULT != null ? ($detail->EFC_RESULT == 1 ? '<span class="text-green-500 ">ผ่าน</span>' : '<span class="text-red-500 ">ไม่ผ่าน</span>') : '-'; 	          
        $detail->EFC_REASON = $detail->EFC_REASON != null ? $detail->EFC_REASON : '-'; 

        
        $detail->PUR_CODE = $detail->PUR_CODE != null ? '('.$detail->PUR_CODE.')' : '';
        $detail->PUR_INCHARGE = $detail->PUR_INCHARGE != null ? $detail->PUR_INCHARGE : '-';
        $detail->PRODUCT_CODE = $detail->PRODUCT_CODE != null ? $detail->PRODUCT_CODE : '-';
        $detail->VENDOR  = $detail->VENDOR != null ? $detail->VENDOR: '-';
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

}