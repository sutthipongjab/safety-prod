<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'models/Gpreport_model.php';
class chemical_model extends Gpreport_model {
    public function __construct(){
        parent::__construct();
    }

    public function getChemicalSec($org){

        return $this->db->select(' AMEC_SDS_ID, CHEMICAL_NAME, REV, EFFECTIVE_DATE, RECEIVED_SDS_DATE, USED_FOR, USED_AREA, KEEPING_POINT, QTY, REC4052, REC4054, CLASS, CLASSNAME')
                        ->from('STY_CHM_SEC')
                        ->where('OWNERCODE', $org)
                        ->where('STATUS', 1)
                        ->order_by('AMEC_SDS_ID ASC')
                        ->get()
                        ->result();
    }

    public function getMaster($section, $status = 1){
        if(!empty($section)){
            // var_dump($section);
            $select = '';
            $pivot  = '';
            foreach($section as $key => $value){
                $owner = substr($value->OWNER,0,-1);
                $select .= "NVL(\"{$owner}\", 'N') AS \"{$owner}\",";
                $pivot .= "'{$value->OWNER}' AS \"{$owner}\",";
            }
            $select = rtrim($select, ',');
            $pivot  = rtrim($pivot, ',');
        }
        $where = $status == 1 ? 'AND C.STATUS = 1 AND S.STATUS = 1' : '';
        $sql = "
        SELECT A.AMEC_SDS_ID, A.RECEIVED_SDS_DATE, A.EFFECTIVE_DATE, A.PRODUCT_CODE, A.CHEMICAL_NAME, A.VENDOR, A.PUR_INCHARGE, A.UN_CLASS, CLASS, A.REV, A.STATUS, $select 
        FROM (
            SELECT C.* , REPLACE(T.TYPE_NAME,'Class ','') AS CLASS, S.OWNER FROM STY_CHEMICAL C
            LEFT JOIN STY_TYPE T ON C.UN_CLASS = T.TYPE_NO AND T.TYPE_CODE = 'CMC'
            LEFT JOIN STY_CHEMICAL_SECTION S ON S.AMEC_SDS_ID = C.AMEC_SDS_ID $where --AND C.STATUS = 1 AND S.STATUS = 1
        ) 
        PIVOT (
            MAX('Y') FOR OWNER IN (
                $pivot
            )
        ) A
        WHERE STATUS = $status";
        return $this->db->query($sql)->result();
    }

    public function getCheAllSec($select = 'OWNER'){
        return $this->db->select($select)
                        ->from('STY_CHEMICAL_REV')
                        ->where('STATUS', '1')
                        ->where('OWNER !=', 'MASTER')
                        ->order_by('OWNER','ASC')
                        ->get()
                        ->result();
    }

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
        $select = '';
        $pivot  = '';
        if(!empty($sec)){
            foreach($sec as $key => $value){
                $select .= "NVL(\"{$value}\", 'N') AS \"{$value}\",";
                $pivot .= "'{$value}.' AS \"{$value}\",";
            }
            $select = rtrim($select, ',');
            $pivot  = rtrim($pivot, ',');
        }
        $sql = "
        SELECT AMEC_SDS_ID, CHEMICAL_NAME, PRODUCT_CODE, EFFECTIVE_DATE, RECEIVED_SDS_DATE, USED_FOR, USED_AREA, KEEPING_POINT, QTY, CLASS, CLASSNAME, REV, 
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
}

