<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'models/Gpreport_model.php';
class status_model extends Gpreport_model {
    public function __construct(){
        parent::__construct();

    }

    public function getStatus(){
        return $this->db->from('STY_STATUS')
                        ->order_by('ST_CODE, ST_NO','ASC')
                        ->get()->result();
    }
}