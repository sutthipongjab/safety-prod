<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'models/Gpreport_model.php';
class user_model extends Gpreport_model {
    public function __construct(){
        parent::__construct();
    }

    public function getOwnOrg($empno){
        return $this->db->from('SEQUENCEORG')
                        ->where('EMPNO', $empno)
                        ->get()
                        ->result();
    }

    public function getUser($empno){
        $this->db->from('AMECUSERALL')
                 ->where('SEMPNO',$empno);
        return $this->db->get()->result();
    }

    public function getOrg($cond){
        foreach($cond as $key => $value){
            $this->set_where($key, $value);
        }
        $this->db->from('ORGANIZATIONS')
                 ->where('CSTATUS', '1')
                 ->where('SSECCODE !=', '00')
                 ->where('SDEPCODE !=', '00');
        return $this->db->get()->result();
    }

    public function getSTManager(){
        return $this->db->select('EMPNO, A.SPOSCODE, A.SNAME, A.SDEPT, A.SPOSNAME')
                    ->from('SEQUENCEORG S')
                    ->join('AMECUSERALL A', 'S.EMPNO = A.SEMPNO')
                    ->where('S.VORGNO', '020601')
                    ->where_in('A.SPOSCODE', array('20','21'))
                    ->where('ROWNUM', '1',false)
                    ->order_by('A.SPOSCODE ASC')
                    ->get()
                    ->result();
    }

    public function getSectionAll(){
        $this->db->distinct()
                 ->select('UPPER(SSEC) as SSEC')
                 ->from('AMECUSERALL')
                 ->where('CSTATUS',1)
                 ->order_by('UPPER(SSEC) ASC');
        return $this->db->get()->result();
    }

    public function getSecByDept($deptcode){
        return $this->db->from('ORGANIZATIONS')
                        ->where('SDEPCODE', $deptcode)
                        ->where('SSEC != ','No Section')
                        ->where('CSTATUS', '1')
                        ->get()
                        ->result();
    }

    public function getDeptAll(){
        return $this->db->distinct()
                        ->select('SDEPT, SDEPCODE')
                        ->from('ORGANIZATIONS')
                        ->where('CSTATUS', '1')
                        ->where('SDEPCODE !=', '00')
                        ->order_by('SDEPT ASC')
                        ->get()
                        ->result();

    }

    public function getDeptDiv($cond = ''){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        $this->db->distinct()
                 ->select('SDIV, SDIVCODE, SDEPT, SDEPCODE')
                 ->from('ORGANIZATIONS')
                 ->where('CSTATUS', '1')
                 ->order_by('SDEPT, SDIV ASC');
        return $this->db->get()->result();
    }

    public function getEFC(){
        return $this->db->from('AMECUSERALL')
                        ->where('SSECCODE', '051002')
                        ->where('CSTATUS', '1')
                        ->where('SPOSCODE <', '40')
                        ->get()
                        ->result();
    }

    public function getBP(){
        return $this->db->from('AMECUSERALL')
                        ->where_in('SSECCODE', ['090502', '090503', '090402', '090602'])
                        ->where('CSTATUS', '1')
                        ->where('SPOSCODE', '40')
                        ->order_by('SSEC','ASC')
                        ->get()
                        ->result();
    }

    public function getSem($seccode){
        return $this->db->select('B.*')
                        ->from('SEQUENCEORG A')
                        ->join('AMECUSERALL B', 'B.SEMPNO = A.EMPNO')
                        ->where('A.VORGNO',$seccode)
                        ->where('A.SPOSCODE', '30')
                        ->get()->result();
    }

    public function getVendor(){
        $v1 = $this->db->select('SVENDCODE AS VENCODE, SENAME AS VENNAME, SEADDRESS AS ADDRESS, STELNO AS TELNO')
                       ->from('PVENDER')
                       ->not_like('SENAME', '%NOT USE%')
                       ->get()->result();
        $v2 = $this->db->select("VENDOR AS VENCODE, VNDNAM AS VENNAME, VNDAD1 || ' ' || VNDAD2 || ' ' || VCITY || ' '|| VPOST AS ADDRESS, VPHONE AS TELNO")
                       ->from('AVM')
                       ->where('VMID','VM')
                       ->get()->result();
        return array_merge($v1,$v2);
    }

}

