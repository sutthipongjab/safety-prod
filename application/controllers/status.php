<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class status extends MY_Controller {


    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Status_model', 'sta');
    }
    

    public function index(){
        $data['title'] = 'status-manage';
        $data['table'] = $this->sta->getAllTable();
        $this->views('status/main',$data);
    }

    public function getData(){
        echo json_encode([
            'data'  => $this->sta->getStatus(),
            'table' => $this->sta->getAllTable(),
            'column' => $this->sta->getAllCol()
        ]);
    }

    public function save(){
        $post = $this->input->post();
        $id = $post['ST_ID'];

        $data = array(
            'ST_CODE'   => strtoupper(trim($post['ST_CODE'])),
            'ST_NO'     => trim($post['ST_NO']),
            'ST_STATUS' => trim($post['ST_STATUS']),
            'ST_TABLE'  => trim($post['ST_TABLE']),
            'ST_COLUMN' => trim($post['ST_COLUMN']),
            'ST_REMARK' => trim($post['ST_REMARK']),
        );

        if(!empty($id)){
            $res = $this->sta->update('STY_STATUS', $data, array('ST_ID' => $id));
        }else{
            $data['ST_ID'] = $this->sta->generate_id('STY_STATUS','ST_ID');
            $res = $this->sta->insert('STY_STATUS',$data);
        }

        $res = array(
            'data'   => $this->sta->getStatus(),
            'status' => $res
        );
        echo json_encode($res);
    }

    public function del(){
        $id = $this->input->post('ST_ID');
        $del = $this->sta->delete('STY_STATUS', array('ST_ID' =>$id));
        $res = array(
            'data'   => $this->sta->getStatus(),
            'status' => $del
        );
        echo json_encode($res);
    }
}