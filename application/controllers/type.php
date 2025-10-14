<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class type extends MY_Controller {


    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Type_model', 'type');
    }
    

    public function index(){
        $data['title'] = 'Manage-Type';
        $data['master'] = $this->type->getMasterName();
        $this->views('typeMaster/type',$data);
    }

    public function getType(){
        echo json_encode($this->type->getType());
    }

    public function save(){
        $id     = $_POST['TYPE_ID'];
        $data = array(
            'TYPE_MASTER'  => trim($_POST['TYPE_MASTER']),
            'TYPE_CODE'    => trim($_POST['TYPE_CODE']),
            'TYPE_NO'      => trim($_POST['TYPE_NO']),
            'TYPE_NAME'    => trim($_POST['TYPE_NAME']),
            'TYPE_DETAIL'  => trim($_POST['TYPE_DETAIL']),
            'TYPE_STATUS'  => trim($_POST['TYPE_STATUS']),
        );

        if(!empty($id)){
            $res = $this->type->update('STY_TYPE', $data, array('TYPE_ID' => $id));
        }else{
            $data['TYPE_ID'] = $this->type->generate_id('STY_TYPE','TYPE_ID');
            $res = $this->type->insert('STY_TYPE',$data);
        }

        $res = array(
            'data'   => $this->type->getType(),
            'status' => $res
        );
        echo json_encode($res);
    }

    public function del(){
        $id = $_POST['id'];
        $del = $this->type->delete('STY_TYPE', array('TYPE_ID' =>$id));
        $res = array(
            'data'   => $this->type->getType(),
            'status' => $del
        );
        echo json_encode($res);
    }
}