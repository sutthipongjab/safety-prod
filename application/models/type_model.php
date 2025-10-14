<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'models/Gpreport_model.php';
class type_model extends Gpreport_model {
    public function __construct(){
        parent::__construct();

    }

    public function getItems(){
        return $this->db->select('A.*, B.TYPE_NAME')
                        ->from('STY_ITEMS A')
                        ->join('STY_TYPE B','A.ITEMS_TYPE = B.TYPE_ID')
                        // ->where ('ITEMS_TYPE',2)
                        ->order_by('ITEMS_TYPE ASC, ITEMS_ID ASC')
                        ->get()->result();
    }

    public function getType($cond = '', $select = '', $distinct = false){
        if($distinct){
            $this->db->distinct();  
        }
        if(!empty($select)){
            $this->db->select($select);
        }
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        return $this->db->from('STY_TYPE')
                        // ->where('TYPE_NO', 99)
                        ->order_by('TYPE_MASTER, TYPE_CODE, TYPE_NO, TYPE_STATUS','ASC')
                        ->get()->result();
    }

    public function getMasterName(){
        return $this->db->distinct()
                        ->select('TYPE_MASTER')
                        ->from('STY_TYPE')
                        ->order_by('TYPE_MASTER','ASC')
                        ->get()->result();
    }

    public function getTypeByCode($typeCode){
        return $this->db->from('STY_TYPE')
                        ->where('TYPE_CODE', $typeCode)
                        ->get()->result();
    }
}