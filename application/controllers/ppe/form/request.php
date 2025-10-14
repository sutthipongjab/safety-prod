<?php
defined('BASEPATH') or exit('No direct script access allowed');


require_once APPPATH . "/third_party/PHPExcel.php";
require_once APPPATH . 'controllers/_file.php';
class Request extends MY_Controller
{

    private $data;
    public function __construct()
    {
        parent::__construct();
        // Load necessary models, libraries, etc.
        $this->load->model('ppe_model', 'ppe');
        $this->load->model('webform_model', 'wf');
        $this->session_expire();
        $this->upload_path   = "//amecnas/AMECWEB/File/" . ($this->_servername() == 'amecweb' ? 'production' : 'development') . "/safety/image/ppe/";
        $this->data['title'] = 'Request';
    }

    public function index()
    {
        // Load the form view
        $this->data['fmst'] = $this->wf->getFormMaster('ST-PPE');
        $this->views('ppe.requisition.index', $this->data);
    }

    public function test_request()
    {
        $this->views('ppe.requisition.user_request');
    }

    public function report()
    {
        $this->data['fmst'] = $this->wf->getFormMaster('ST-PPE');
        $this->views('ppe.requisition.report', $this->data);
    }

    public function get_category()
    {
        $data = $this->ppe->get_category();
        foreach ($data as $key => $item) {
            if ($item->CATIMAGE != null) {
                $item->CATIMAGE = $this->conVBase64($this->upload_path . $item->CATIMAGE);
            } else {
                $item->CATIMAGE = base_url('assets/images/no-image.png');
            }
            // $item->PIC = $this->conVBase64($this->upload_path . $item->PCAT_PIC);
        }
        echo json_encode($data);
    }



    public function get_uniform_by_cat()
    {
        $cat_id = $this->input->post('CATID');
        $data   = $this->ppe->get_uniform_by_cat($cat_id);
        echo json_encode($data);
    }

    public function get_uniform()
    {
        $id   = $this->input->post('PROD_ID');
        $data = $this->ppe->get_uniform($id);
        echo json_encode($data);
    }



    public function test()
    {

        $filePath    = 'data_ppe.xlsx'; // ไฟล์ที่ต้องการอ่าน
        $spreadsheet = PHPExcel_IOFactory::load($filePath);
        $sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // แยกชื่อคอลัมน์ (แถวแรก)
        $column_name = $sheetData[1];
        echo "<pre>";
        print_r($column_name);  // แสดงชื่อคอลัมน์
        echo "</pre>";

        // นำข้อมูลจากแถวที่ 2 เป็นต้นไป
        $data = [];
        for ($i = 2; $i <= count($sheetData); $i++) {
            $row = $sheetData[$i];

            // สร้าง array ที่มีชื่อคอลัมน์เป็น key
            $data = [
                $column_name['A'] => $row['A'],  // ค่าจากคอลัมน์ A
                $column_name['B'] => $row['B'],  // ค่าจากคอลัมน์ B
                $column_name['C'] => $row['C'],  // ค่าจากคอลัมน์ C
                $column_name['D'] => $row['D'],  // ค่าจากคอลัมน์ D
                $column_name['E'] => $row['E'],  // ค่าจากคอลัมน์ E
                $column_name['F'] => $row['F'],  // ค่าจากคอลัมน์ F
            ];

            $this->ppe->insert_ppe('UNIFORM', $data);
        }

    }

    public function get_reason()
    {
        $data = $this->ppe->get_request_type();
        echo json_encode($data);
    }

    public function get_data_user()
    {
        $empno = $this->input->post('empno');
        $data  = $this->ppe->get_data_user($empno);
        echo json_encode($data);
    }

    public function insert_request()
    {
        $data   = $this->input->post('data');
        $NFRMNO = $this->input->post('NFRMNO');
        $VORGNO = $this->input->post('VORGNO');
        $CYEAR  = $this->input->post('CYEAR');
        $CYEAR2 = $this->input->post('CYEAR2');
        $NRUNNO = $this->input->post('NRUNNO');
        $req    = $this->input->post('req');
        $key    = $this->input->post('key');

        $data_request = [
            'NFRMNO'      => $NFRMNO,
            'VORGNO'      => $VORGNO,
            'CYEAR'       => $CYEAR,
            'CYEAR2'      => $CYEAR2,
            'NRUNNO'      => $NRUNNO,
            'EMP_INPUT'   => $key,
            'EMP_REQUEST' => $req,
        ];
        $this->ppe->insert_ppe('UNIFORM_REQUEST', $data_request);
        print_r($data_request);
        //URD_ID|NFRMNO|VORGNO|CYEAR|CYEAR2|NRUNNO|UNIFORM_CATEGORY|UNIFORM_TYPE|UNIFORM_OLD_TYPE|QTY|REQUEST_TYPE|
        foreach ($data as $key => $value) {
            $data_req_detail = [
                'URD_ID'           => $this->ppe->get_id_request()->id + 1,
                'NFRMNO'           => $NFRMNO,
                'VORGNO'           => $VORGNO,
                'CYEAR'            => $CYEAR,
                'CYEAR2'           => $CYEAR2,
                'NRUNNO'           => $NRUNNO,
                'UNIFORM_CATEGORY' => $value['category'],
                'UNIFORM_TYPE'     => $value['size'],
                'QTY'              => $value['qty'],
                'REQUEST_TYPE'     => $value['request_type'],
            ];
            print_r($data_req_detail);
            $this->ppe->insert_ppe('UNIFORM_REQUEST_DETAIL', $data_req_detail);
        }
        // print_r($data);
        // $this->ppe->insert_ppe('UNIFORM_REQUEST', $data);
    }

    public function get_stock(){
        $id = $this->input->post('PROD_ID');
        
    }
}
?>