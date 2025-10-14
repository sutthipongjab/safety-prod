<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_file.php';
class inspection extends MY_Controller {

    use _File;

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Area_model', 'area');
        // $this->load->model('Type_model', 'type');
        $this->load->model('Webform_model', 'wf');
        $this->load->model('Form_model', 'frm');
        $this->upload_path = "//amecnas/AMECWEB/File/" .($this->_servername()=='amecweb' ? 'production' : 'development') ."/safety/image/electric/";
    }
    
    public function index(){
        $data['title'] = 'electric-inspection';
        $data['area']  = $this->area->getAreas(['AREA_CATEGORY_CODE' => 'ET']);
        $data['formInfo'] = $this->wf->getFormMaster('ST-ECS');
        $this->views('electric/inspection',$data);
    }

    public function createForm(){
        $post = $this->input->post();
        $data = [
            'NFRMNO' => $post['formtype'],
            'VORGNO' => $post['owner'],
            'CYEAR'  => $post['cyear'],
            'CYEAR2' => $post['cyear2'],
            'NRUNNO' => $post['runno'],
            'FORM_CATEGORY' => $post['FORM_CATEGORY'],
            'FORM_NO'       => $post['FORM_NO'],
            'FORM_AREA'     => $post['FORM_AREA'],
            'FORM_REQUEST'  => $post['FORM_REQUEST'],
            'FORM_INPUT'    => $post['FORM_INPUT'],
        ];
        $status = $this->frm->insert('STY_FORM',$data);
        echo json_encode(['data' => $data, 'status' => $status]);
    }



    // function add_column_if_not_exists($table, $column, $attributes) {
    //      // เรียกใช้งาน
    // add_column_if_not_exists('your_table', 'new_column_name', [
    //     'type' => 'VARCHAR',
    //     'constraint' => '255',
    //     'null' => FALSE,
    //     'default' => ''
    // ]);
    //     $CI =& get_instance();
    //     $fields = $CI->db->list_fields($table);
    
    //     // ถ้ายังไม่มีคอลัมน์ ให้เพิ่มเข้าไป
    //     if (!in_array($column, $fields)) {
    //         $CI->load->dbforge();
    //         $CI->dbforge->add_column($table, [$column => $attributes]);
    //     }
    // }
    
   

}