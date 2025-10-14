<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'models/Gpreport_model.php';
class form_model extends Gpreport_model {
    public function __construct(){
        parent::__construct();

    }

    public function getMaster($cond = ''){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        $this->db->from('STY_FORM_MASTER')
                 ->order_by('FRM_CATEGORY, FRM_NO', 'ASC');
        return $this->db->get()->result();
    }

    public function getTopic($cond = ''){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        $this->db->from('STY_FORM_TOPIC')
                 ->order_by('FRMT_CATEGORY, FRMT_RUNNO, FRMT_NO', 'ASC');
        return $this->db->get()->result();
    }

    public function getDetail($cond = ''){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        $this->db->from('STY_FORM_DETAIL')
                 ->order_by('FRMD_CATEGORY, FRMD_RUNNO, FRMD_NO, FRMD_SEQ', 'ASC');
        return $this->db->get()->result();
    }
   
    public function getFormMaster($cond = ''){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        $this->db->from('STY_FORMMASTER')
                 ->order_by('CATEGORY, FORMNO', 'ASC');
        return $this->db->get()->result();
    }

    public function getFormDetail($cond = ''){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        $this->db->from('STY_FORMDETAIL')
                 ->order_by('CATEGORY, TOPIC_NO, SEQ', 'ASC');
        return $this->db->get()->result();
    }

    public function getFormImage($cond = ''){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        $this->db->from('STY_FORM_IMAGE')
                 ->order_by('FRMI_CATEGORY, FRMI_RUNNO, FRMI_NO, FRMI_ID', 'ASC');
        return $this->db->get()->result();
    }

    public function getForm($cond = ''){
        if($cond != ''){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        $this->db->from('STY_FORM')
                 ->order_by('NRUNNO ASC');
        return $this->db->get()->result();
    }

    public function getFormByMonth($dateStart, $dateEnd, $code){
        return $this->db->select('T.TYPE_CODE AS FORM_TYPE, A.AREA_MANAGER, A.AREA_NAME, FRM.CST, F.*')
                        ->from('STY_FORM F')
                        ->join('STY_TYPE T', 'F.FORM_CATEGORY = T.TYPE_ID')
                        ->join('STY_AREA A', 'F.FORM_AREA  = A.AREA_ID')
                        ->join('FORM FRM', 'F.NFRMNO = FRM.NFRMNO AND F.VORGNO = FRM.VORGNO AND F.CYEAR = FRM.CYEAR AND F.CYEAR2 = FRM.CYEAR2 AND F.NRUNNO = FRM.NRUNNO')
                        ->where('F.FORM_CREATE >=',"TO_DATE('$dateStart 00:00:00', 'DD/MM/YYYY HH24:MI:SS')", false)
                        ->where('F.FORM_CREATE <=',"TO_DATE('$dateEnd 23:59:59', 'DD/MM/YYYY HH24:MI:SS')", false)
                        ->where('T.TYPE_CODE', $code)
                        ->order_by('F.NRUNNO ASC')
                        ->get()->result();
    }
}
    
