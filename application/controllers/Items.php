<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class items extends MY_Controller {


    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Items_model', 'itm');
    }
    

    public function index(){
        $data['title'] = 'itemsMaster';
        $data['type'] = $this->itm->getType();
        $this->views('itemsMaster/items',$data);
    }

    public function getItems(){
        $items = $this->itm->getItems();
        echo json_encode($items);
    }

    public function save(){
        $userno = $_POST['userno'];
        $id     = $_POST['ITEMS_ID'];

        $data = array(
            'ITEMS_TYPE'       => $_POST['ITEMS_TYPE'],
            'ITEMS_NAME'       => $_POST['ITEMS_NAME'],
            'ITEMS_ENAME'      => $_POST['ITEMS_ENAME'],
        );

        if(!empty($id)){
            $data['ITEMS_USERUPDATE'] = $userno;
            $data['ITEMS_DATEUPDATE'] = 'sysdate';
            $res = $this->itm->update('STY_ITEMS', $data, array('ITEMS_ID' => $id));
        }else{
            $data['ITEMS_ID'] = $this->itm->generate_id('STY_ITEMS','ITEMS_ID');
            $data['ITEMS_USERCREATE'] = $userno;
            $res = $this->itm->insert('STY_ITEMS',$data);
        }

        $res = array(
            'data'   => $this->itm->getItems(),
            'status' => $res
        );
        echo json_encode($res);
    }

    public function del(){
        $id = $_POST['id'];
        $del = $this->itm->delete('STY_ITEMS', array('ITEMS_ID' =>$id));
        $res = array(
            'data'   => $this->itm->getItems(),
            'status' => $del
        );
        echo json_encode($res);
    }
}