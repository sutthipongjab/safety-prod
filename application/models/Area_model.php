<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'models/Gpreport_model.php';
class area_model extends Gpreport_model {
    public function __construct(){
        parent::__construct();

    }

    public function getAreas($cond = ''){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        $this->db->from('STY_AREAMASTER')
                 ->order_by('AREA_ID');
        return $this->db->get()->result();
    }

    private function generate_area_id(){
        $data = $this->db->select('NVL(MAX(AREA_ID),0) AS ID')->get('STY_AREA')->result();
        return $data[0]->ID+1;
    }

    // public function getSection(){
    //     $this->db->distinct()
    //              ->select('UPPER(SSEC) as SSEC')
    //              ->from('AMECUSERALL')
    //              ->where('CSTATUS',1)
    //              ->order_by('UPPER(SSEC) ASC');
    //     return $this->db->get()->result();
    // }

    public function upsertArea($data, $userno){
        if($data['AREA_ID'] == ''){
            $id = $this->generate_area_id();
            $data['AREA_ID']         = $id;
            $data['AREA_USERCREATE'] = $userno;
            $res = $this->db->insert('STY_AREA', $data);
        }else{
            $data['AREA_USERUPDATE'] = $userno;
            $this->db->set('AREA_DATEUPDATE', 'sysdate', false);
            $this->db->where('AREA_ID',$data['AREA_ID']);
            $res = $this->db->update('STY_AREA',$data);
        }
        // return $this->getAreas();
        return $res;
    }

    public function deleteArea($id){
        $this->db->where('AREA_ID',$id);
        return $this->db->delete('STY_AREA');
    }

    public function getArea($id){
        $this->db->from('STY_AREAMASTER')
                 ->where('AREA_ID', $id);
        return $this->db->get()->result();
    }

    public function getUserdata($empno){
        return $this->db->from('AMECUSERALL')
                 ->where('SEMPNO', $empno)->get()->result();
    }
}