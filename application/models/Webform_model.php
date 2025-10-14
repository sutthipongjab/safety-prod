<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'models/Gpreport_model.php';
class Webform_model extends Gpreport_model {
    private $FLOW_RUNNING 	= "1";
	private $FLOW_PREPARE 	= "0";
	public $STEP_READY 	= "3";
	private $STEP_WAIT		= "2";
	private $STEP_APPROVE   = "5";
	private $STEP_REJECT 	= "6";

    public function __construct(){
        parent::__construct();
        $this->db = $this->load->database('WEBFORM',true);
    }

    public function getFormMaster($VANAME){
        $this->db->from('FORMMST')
				 ->where('VANAME', $VANAME);
		return $this->db->get()->result();
    }

    public function getEmpFlow($form, $empno){
        foreach($form as $key => $val){
            $this->set_where($key, $val);
        }
        $this->db->from('FLOW')
                 ->where("(vapvno = '$empno' or vrepno = '$empno') ", null, false)
                 ->where('CSTEPST', $this->STEP_READY);
        return $this->db->get()->result();
    }

    public function getFormName($frmNo, $orgNo, $y){
	    $q = "select VANAME from webform.formMst where nNo = '" . $frmNo . "' and vOrgNo = '" . $orgNo . "' and cYear = '" . $y . "'";
		return $this->db->query($q)->result();
	}

    /**
     * @param array $form 
     * @param string $apv The empno
     * @return array 
     */
    public function getExtdata($form, $apv){
        foreach($form as $key => $val){
            $this->set_where($key, $val);
        }
        $this->db->select('CEXTDATA')
                 ->from('FLOW')
                 ->where("(vapvno = '$apv' or vrepno = '$apv') ", null, false)
                 ->where('CSTEPST', $this->STEP_READY);
        return $this->db->get()->result();
		//$q = "select CEXTDATA FROM FLOW where nfrmno = '".$no."' and vorgno = '".$orgNo."' and cyear = '".$y."' and cyear2 = '".$y2."' and nrunno = '".$runNo."' and (vapvno = '".$apv."' or vrepno = '".$apv."') and cstepst = '".$this->STEP_READY."'";
		//return $this->db->query($q)->result();
	}

    /**
     * get data apv by CEXTDATA
     * @param array $form 
     * @param string $CEXTDATA 
     * @return array 
     */
    public function getApvData($form, $CEXTDATA){
        foreach($form as $key => $val){
            $this->set_where($key, $val);
        }
        $this->db->from('FLOW')
                 ->where('CEXTDATA', $CEXTDATA);
        return $this->db->get()->result();
	}

    public function getCSETPNO($cond){
        foreach($cond as $key => $val){
            $this->set_where($key, $val);
        }
        $this->db->select('CSTEPNO')
                 ->from('FLOW');
        return $this->db->get()->result();
    }

    /**
     * @param array $cond
     * @param array $extData ['01','02']
     * @return int
     */
    public function deleteExtra($cond, $extData) {
        foreach($cond as $key => $val) {
            $this->set_where($key, $val);
        }
        $this->db->where_in('CEXTDATA', $extData);
        $this->db->delete('FLOW');
        return $this->db->affected_rows();
    }

    /**
     * Get mail sem patrol >= 14 days
     */
    public function getMailSemP(){
        $this->db->select("'ST-INP'||SUBSTR(FM.CYEAR2,3,2)||'-'||LPAD(FM.NRUNNO, 6, '0') AS FORMNO, 
		FM.NFRMNO, FM.VORGNO, FM.CYEAR, FM.CYEAR2, FM.NRUNNO,
		FL.VAPVNO AS SEM, 
		A.SRECMAIL, 
		FL.DAPVDATE AS SEM_APV, 
		FL2.DAPVDATE AS SAFETY_APV, 
		TRUNC(SYSDATE) AS CURRENT_DATE, 
		TRUNC(SYSDATE) - FL2.DAPVDATE AS DAYS")
                 ->from('FORMMST MT')
                 ->join('FORM FM', 'FM.CYEAR = MT.CYEAR AND FM.NFRMNO = MT.NNO AND FM.VORGNO = MT.VORGNO')
                 ->join('FLOW FL', "FL.CYEAR = FM.CYEAR AND FL.CYEAR2 = FM.CYEAR2 AND FL.NFRMNO = FM.NFRMNO AND FL.NRUNNO = FM.NRUNNO AND FL.VORGNO = FM.VORGNO AND FL.CEXTDATA = '01' AND FL.DAPVDATE IS NULL AND FL.CSTEPST = '3'")
                 ->join('FLOW FL2', 'FL2.CYEAR = FM.CYEAR AND FL2.CYEAR2 = FM.CYEAR2 AND FL2.NFRMNO = FM.NFRMNO AND FL2.NRUNNO = FM.NRUNNO AND FL2.VORGNO = FM.VORGNO AND FL2.CSTEPNEXTNO = FL.CSTEPNO AND FL2.DAPVDATE IS NOT NULL')
                 ->join('AMECUSERALL A', 'A.SEMPNO = FL.VAPVNO')
                 ->where("VANAME = 'ST-INP' AND CST = '1' AND TRUNC(SYSDATE) - FL2.DAPVDATE  >= 14");
        
        return $this->db->get()->result();
    }

    public function checkReturnb($form, $CSTEPNO){
        foreach($form as $key => $val){
            $this->set_where($key, $val);
        }
        $this->db->from('FLOW')
                 ->where('VREMOTE IS NOT NULL')
                 ->where('CSTEPNO', $CSTEPNO);
        return $this->db->get()->result();
    }

    public function getFormOnExData($frmNo, $orgNo, $y, $CEXTDATA){
        $this->db->from('FLOW')
                 ->where('NFRMNO', $frmNo)
                 ->where('VORGNO', $orgNo)
                 ->where('CYEAR', $y)
                 ->where('CEXTDATA', $CEXTDATA)
                 ->where('CSTEPST', $this->STEP_READY);
        return $this->db->get()->result();
    }


}