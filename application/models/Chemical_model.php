<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'models/Gpreport_model.php';
class chemical_model extends Gpreport_model {
    public function __construct(){
        parent::__construct();
    }

    // public function getOwnOrg($empno){
    //     return $this->db->from('SEQUENCEORG')
    //                     ->where('EMPNO', $empno)
    //                     ->get()
    //                     ->result();
    // }

    // public function getUser($empno){
    //     $this->db->from('AMECUSERALL')
    //              ->where('SEMPNO',$empno);
    //     return $this->db->get()->result();
    // }

    // public function getOrg($cond){
    //     foreach($cond as $key => $value){
    //         $this->set_where($key, $value);
    //     }
    //     $this->db->from('ORGANIZATIONS')
    //              ->where('CSTATUS', '1')
    //              ->where('SSECCODE !=', '00')
    //              ->where('SDEPCODE !=', '00');
    //     return $this->db->get()->result();
    // }

    public function getChemicalSec($org){

        return $this->db->select(' AMEC_SDS_ID, CHEMICAL_NAME, REV, EFFECTIVE_DATE, RECEIVED_SDS_DATE, USED_FOR, USED_AREA, KEEPING_POINT, QTY, REC4052, REC4054, CLASS')
                        ->from('STY_CHM_SEC')
                        // ->where("REPLACE(\"OWNER\", ' ', '') =  '$org'")
                        ->where('OWNERCODE', $org)
                        ->where('STATUS', 1)
                        ->order_by('AMEC_SDS_ID ASC')
                        ->get()
                        ->result();
        // return $this->db->select(' C.AMEC_SDS_ID, C.CHEMICAL_NAME, S.REV, C.EFFECTIVE_DATE, S.RECEIVED_SDS_DATE, S.USED_FOR, S.USED_AREA, S.KEEPING_POINT, S.QTY, S.REC4052, S.REC4054, S.CLASS')
        //                 ->from('STY_CHEMICAL_SECTION S')
        //                 ->join('STY_CHEMICAL C', 'C.AMEC_SDS_ID = S.AMEC_SDS_ID  AND C.STATUS = 1 AND S.STATUS = 1')
        //                 ->where("REPLACE(\"OWNER\", ' ', '') =  '$org'")
        //                 // ->where('STATUS', '1')
        //                 ->order_by('C.AMEC_SDS_ID ASC')
        //                 ->get()
        //                 ->result();
    }

    public function getMaster($section, $status = 1){
        if(!empty($section)){
            // var_dump($section);
            $select = '';
            $pivot  = '';
            foreach($section as $key => $value){
                $owner = substr($value->OWNER,0,-1);
                $select .= "NVL(\"{$owner}\", 'N') AS \"{$owner}\",";
                // $pivot  .= "'$value->OWNER' AS '$owner',";
                $pivot .= "'{$value->OWNER}' AS \"{$owner}\",";
            }
            $select = rtrim($select, ',');
            $pivot  = rtrim($pivot, ',');
        }
        $where = $status == 1 ? 'AND C.STATUS = 1 AND S.STATUS = 1' : '';
        $sql = "
        SELECT A.AMEC_SDS_ID, A.RECEIVED_SDS_DATE, A.EFFECTIVE_DATE, A.PRODUCT_CODE, A.CHEMICAL_NAME, A.VENDOR, A.PUR_INCHARGE, A.UN_CLASS, A.REV, A.STATUS, $select 
        FROM (
            SELECT C.* , S.OWNER FROM STY_CHEMICAL C
            LEFT JOIN STY_CHEMICAL_SECTION S ON S.AMEC_SDS_ID = C.AMEC_SDS_ID $where --AND C.STATUS = 1 AND S.STATUS = 1
        ) 
        PIVOT (
            MAX('Y') FOR OWNER IN (
                $pivot
            )
        ) A
        WHERE STATUS = $status";
        return $this->db->query($sql)->result();
        // return $this->db->from('STY_CHEMICAL')
        //                 ->where('STATUS', '1')
        //                 ->order_by('AMEC_SDS_ID')
        //                 ->get()
        //                 ->result();
    }

    public function getCheAllSec($select = 'OWNER'){
        // return $this->db->distinct()
        //                 ->select('OWNER')
        //                 ->from('STY_CHEMICAL_SECTION')
        //                 ->where('STATUS', '1')
        //                 ->order_by('OWNER','ASC')
        //                 ->get()
        //                 ->result();
        return $this->db->select($select)
                        ->from('STY_CHEMICAL_REV')
                        ->where('STATUS', '1')
                        ->where('OWNER !=', 'MASTER')
                        ->order_by('OWNER','ASC')
                        ->get()
                        ->result();
    }

    // public function getManager(){
    //     return $this->db->select('EMPNO, A.SPOSCODE, A.SNAME, A.SDEPT, A.SPOSNAME')
    //                 ->from('SEQUENCEORG S')
    //                 ->join(' AMECUSERALL A', 'S.EMPNO = A.SEMPNO')
    //                 ->where('S.VORGNO', '020601')
    //                 ->where_in('S.SPOSCODE', array('20','21'))
    //                 ->where('ROWNUM', '1',false)
    //                 ->order_by('S.SPOSCODE ASC')
    //                 ->get()
    //                 ->result();
    // }

    public function getRev($cond=''){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        return $this->db->from('STY_CHEMICAL_REV')
                        ->where('STATUS', '1')
                        ->order_by('OWNER','ASC')
                        ->get()
                        ->result();
    }

    public function getSecRebuild($id, $sec){
        if(!empty($sec)){
            // var_dump($section);
            $select = '';
            $pivot  = '';
            foreach($sec as $key => $value){
                // $owner = substr($value->OWNER,0,-1);
                $select .= "NVL(\"{$value}\", 'N') AS \"{$value}\",";
                // $pivot  .= "'$value->OWNER' AS '$owner',";
                $pivot .= "'{$value}.' AS \"{$value}\",";
            }
            $select = rtrim($select, ',');
            $pivot  = rtrim($pivot, ',');
        }
        $sql = "
        SELECT AMEC_SDS_ID, CHEMICAL_NAME, PRODUCT_CODE, EFFECTIVE_DATE, RECEIVED_SDS_DATE, USED_FOR, USED_AREA, KEEPING_POINT, QTY, CLASS, REV, 
        $select
        FROM 
        (
            SELECT * FROM STY_CHM_SEC
        )
        PIVOT
        (
            MAX('Y') FOR OWNER IN ( 
                $pivot
            )
        )  
        WHERE STATUS = 0
        AND AMEC_SDS_ID IN ($id)";
        return $this->db->query($sql)->result();
        // SELECT C.AMEC_SDS_ID, S.OWNER, C.EFFECTIVE_DATE, S.RECEIVED_SDS_DATE, C.CHEMICAL_NAME, C.PRODUCT_CODE, S.USED_FOR, S.USED_AREA, S.KEEPING_POINT, S.QTY, S.CLASS, S.REV, C.STATUS 
        //     FROM STY_CHEMICAL C
        //     JOIN STY_CHEMICAL_SECTION S ON C.AMEC_SDS_ID = S.AMEC_SDS_ID
    }

    public function getChemical($cond = ''){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        return $this->db->from('STY_CHEMICAL')
                        ->order_by('AMEC_SDS_ID', 'ASC')
                        ->get()
                        ->result();
    }

    public function getCHMSec($cond = ''){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        return $this->db->from('STY_CHM_SEC')
                        ->order_by('AMEC_SDS_ID', 'ASC')
                        ->get()
                        ->result();
    }

    // public function getSecByDept($deptcode){
    //     return $this->db->from('ORGANIZATIONS')
    //                     ->where('SDEPCODE', $deptcode)
    //                     ->where('SSEC != ','No Section')
    //                     ->where('CSTATUS', '1')
    //                     ->get()
    //                     ->result();
    // }

    public function getOwnerByCode($code){
        return $this->db->from('STY_CHEMICAL_REV')
                        ->where('OWNERCODE', $code)
                        ->get()
                        ->result();
    }

    public function getFormData($cond){
        foreach($cond as $key => $value) {
            $this->set_where($key, $value);
        }
        return $this->db->from('STY_CHEMICAL_FORM')
                        ->get()
                        ->result();
    }

    public function getFile($id){
        return $this->db->from('STY_FILES F')
                //  ->join('STY_TYPE T', 'F.TYPE_ID = T.TYPE_ID')
                 ->where('FILE_ID', $id)
                 ->get()
                 ->result();
    }

    public function checkMasterSec($code){
        return $this->db->from('STY_CHEMICAL_REV')
                        ->where($code)
                        ->get()
                        ->result();
    }

    public function checkDataSec($cond){
        return $this->db->from('STY_CHEMICAL_SECTION')
                        ->where($cond)
                        ->get()
                        ->result();
    }
    
    public function checkDataDetail($cond){
        return $this->db->from('STY_CHEMICAL_SECDETAIL')
                        ->where($cond)
                        ->get()
                        ->result();
    }

    public function getDataStamp($owner){
        return $this->db->select('R.OWNER, R.OWNERCODE, R.USER_APPROVE AS EMPNO, A.SPOSCODE, A.SNAME, A.SDEPT, A.SPOSNAME, R.APPROVE_DATE')
                        ->from('STY_CHEMICAL_REV R')
                        ->join('AMECUSERALL A', 'R.USER_APPROVE = A.SEMPNO')
                        ->like('UPPER(R.OWNER)', $owner)
                        ->where('R.STATUS', 1)
                        ->get()
                        ->result();
    }

    public function getFollow($fyear){
        $this->db->select('CF.*, F.CST')
                 ->from('STY_CHEMICAL_FORM CF')
                 ->join('FORM F', 'F.NFRMNO = CF.NFRMNO AND F.VORGNO = CF.VORGNO AND F.CYEAR = CF.CYEAR AND F.CYEAR2 = CF.CYEAR2 AND F.NRUNNO = CF.NRUNNO')
                 ->where("TO_CHAR(CF.CREATE_DATE, 'YYYY') =  $fyear")
                 ->order_by('CF.NRUNNO ASC');
        return $this->db->get()->result();
    }

    // public function checkMaster($cond){
    //     return $this->db->from('STY_CHEMICAL')
    //                     ->where($cond)
    //                     ->get()
    //                     ->result();
    // }

    // public function getEFC(){
    //     return $this->db->from('AMECUSERALL')
    //                     ->where('SSECCODE', '051002')
    //                     ->where('CSTATUS', '1')
    //                     ->where('SPOSCODE <', '40')
    //                     ->get()
    //                     ->result();
    // }

    // public function getBP(){
    //     return $this->db->from('AMECUSERALL')
    //                     ->where_in('SSECCODE', ['090502', '090503', '090402', '090602'])
    //                     ->where('CSTATUS', '1')
    //                     ->where('SPOSCODE', '40')
    //                     ->order_by('SSEC','ASC')
    //                     ->get()
    //                     ->result();
    // }

   

    // public function getVendor(){
    //     $v1 = $this->db->select('SVENDCODE AS VENCODE, SENAME AS VENNAME, SEADDRESS AS ADDRESS, STELNO AS TELNO')
    //                    ->from('PVENDER')
    //                    ->not_like('SENAME', '%NOT USE%')
    //                    ->get()->result();
    //     $v2 = $this->db->select("VENDOR AS VENCODE, VNDNAM AS VENNAME, VNDAD1 || ' ' || VNDAD2 || ' ' || VCITY || ' '|| VPOST AS ADDRESS, VPHONE AS TELNO")
    //                    ->from('AVM')
    //                    ->where('VMID','VM')
    //                    ->get()->result();
    //     return array_merge($v1,$v2);
    // }
}

