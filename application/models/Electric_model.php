<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'models/Gpreport_model.php';
class electric_model extends Gpreport_model {
    public function __construct(){
        parent::__construct();
    }

    public function checkFormCreate($id){
        return $this->db->from('STY_FORM')
                        ->where("TO_CHAR(FORM_CREATE, 'MM') = ".date('m'))
                        // ->where("TO_CHAR(FORM_CREATE, 'MM') = 05") // test
                        ->where('FORM_AREA', $id)
                        ->get()
                        ->result();
    }

    // public function getForm($cond){
    //     if(!empty($cond)){
    //         foreach($cond as $key => $value){
    //             $this->set_where($key, $value);
    //         }
    //     }
    //     $this->db->from('STY_ELECTRIC_INSPECTION')
    //              ->order_by('ET_TOPIC, ET_SEQ', 'ASC');
    //     return $this->db->get()->result();
    // }

    public function getForm($cond){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        $this->db->select('E.*, T.TYPE_NAME AS DATA_TYPE')
                 ->from('STY_ELECTRIC_INSPECTION E')
                 ->join('STY_FORM_DETAIL D', 'E.ET_TOPIC = D.FRMD_NO AND E.ET_SEQ = D.FRMD_SEQ AND E.ET_CATEGORY  = D.FRMD_CATEGORY  AND E.ET_NO  = D.FRMD_RUNNO')
                 ->join('STY_TYPE T', 'D.FRMD_TYPE = T.TYPE_ID')
                 ->order_by('ET_TOPIC, ET_SEQ', 'ASC');
        return $this->db->get()->result();
    }  

    public function checkData($cond){
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        return $this->db->from('STY_ELECTRIC_INSPECTION')
                        ->get()
                        ->result();
    }
}