<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_file.php';
class area extends MY_Controller {

    use _File;

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Area_model', 'area');
        $this->load->model('Type_model', 'type');
        $this->load->model('User_model', 'usr');
        $this->upload_path = "//amecnas/AMECWEB/File/" .($this->_servername()=='amecweb' ? 'production' : 'development') ."/safety/image/areaMaster/";
    }
    
    // private function _servername(){
    //     return strtolower(preg_replace('/\d+/u', '', gethostname()));
    // }

    public function index(){
        // var_dump($_SESSION['user']);
        $data['title'] = 'safetyArea';
        // $data['section'] = $this->area->allSection();
        // $data['dept']    = $this->usr->getDeptAll();
        $data['org']  = $this->usr->getDeptDiv();
        $data['type'] = $this->type->getTypeByCode('AT');
        $data['category'] = $this->type->getType(['TYPE_NO'=>99]);
        $this->views('areaMaster/area',$data);
    }

    public function getArea(){
        // $area = $this->area->getAreas();
        $area = $this->dataAreaConvert();
        echo json_encode($area);
    }

    public function getUserdata(){
        $empno    = $_POST['empno'];
        $organize = $this->area->getUserdata($empno);
        echo json_encode($organize);
    }

    private function dataAreaConvert(){
        // $mimeType = 'image/png';
        // $area = $this->area->getAreas();
        // foreach ($area as $key => $a) {
        //     # code...
        //     $a->baseURL = !empty($a->IMAGE_FILE) ? 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($a->IMAGE_FILE)) : '';
        // }
        // return $area;
        $area = $this->area->getAreas();
        foreach ($area as $key => $a) {
            # code...
            $a->baseURL =$this->conVBase64($a->IMAGE_FILE);
            // $a->baseURL = !empty($a->IMAGE_FILE) ? 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($a->IMAGE_FILE)) : '';
        }
        return $area;
    }

    public function save(){
        $msg = '';
        $userno = $_POST['userno'];
        $id     = $_POST['AREA_ID'];
        $this->area->trans_start();

        $data = array(
            'AREA_ID'         => $id,
            'AREA_NAME'       => $_POST['AREA_NAME'],
            'AREA_ENAME'      => $_POST['AREA_ENAME'],
            'AREA_EMPNO'      => $_POST['AREA_EMPNO'],
            'AREA_MANAGER'      => $_POST['AREA_MANAGER'],
            'AREA_MANAGERCODE'  => $_POST['AREA_MANAGERCODE'],
            'AREA_OWNER'      => $_POST['AREA_OWNER'],
            'AREA_DIV'        => $_POST['AREA_DIV'],
            'AREA_DIVCODE'    => $_POST['AREA_DIVCODE'],
            'AREA_DEPT'       => $_POST['AREA_DEPT'],
            'AREA_DEPTCODE'   => $_POST['AREA_DEPTCODE'],
            'AREA_SEC'        => $_POST['AREA_SEC'],
            'AREA_SECCODE'    => $_POST['AREA_SECCODE'],
            'AREA_TYPE'       => $_POST['AREA_TYPE'],
            'AREA_CATEGORY'   => $_POST['AREA_CATEGORY'],
        );

        if(isset($_FILES['AREA_IMAGE'])){
            $file = $this->uploadfile($_FILES['AREA_IMAGE']);
            if($file['status'] == 'success'){
                $data['AREA_IMAGE'] = $this->setImageDel($file, $userno, $id);
            }else{
                if($_POST['deleteImage'] == 1){
                    $area = $this->area->getArea($id);
                    if(!empty($area[0]->IMAGE_ID)){
                        $this->deleteFile($area[0]->IMAGE_FNAME);
                        $this->area->delete('STY_IMAGE', ['IMAGE_ID' => $area[0]->IMAGE_ID]);
                        $data['AREA_IMAGE'] = null;
                    }
                }
            }
        } else {
            $data['AREA_IMAGE'] = null;
        }
        $result = $this->area->upsertArea($data, $userno);
        $res = array(
            'data'   => $this->dataAreaConvert(),
            'status' => $result
        );
        $this->area->trans_complete();
        echo json_encode($res);
    }

    /**
     * set data for insert or update in STY_IMAGE
     * @author sutthipong tangmongkhoncharoen
     * @since  2024-10-12
     * @param array $data 
     * @param string $userno
     * @param string $id     
     */
    private function setImageDel($data, $userno, $id){
        $d = array(
            'IMAGE_ONAME' => $data['file_origin_name'],
            'IMAGE_FNAME' => $data['file_name'],
            'TYPE_ID'     => $this->type->getTypeByCode('AM')[0]->TYPE_ID, // AREA_MASTER
            'IMAGE_PATH'  => $this->upload_path
        );
        $area = $this->area->getArea($id);
        if(!empty($area[0]->IMAGE_ID)){
            $this->deleteFile($area[0]->IMAGE_FNAME);
            $d['IMAGE_USERUPDATE'] = $userno;
            $d['IMAGE_DATEUPDATE'] = 'sysdate';
            $update = $this->area->update('STY_IMAGE', $d, array('IMAGE_ID' => $area[0]->IMAGE_ID));
            return $area[0]->IMAGE_ID;
        }else{
            $d['IMAGE_USERCREATE'] = $userno;
            $d['IMAGE_ID']         = $this->area->generate_id('STY_IMAGE', 'IMAGE_ID');
            $this->area->insert('STY_IMAGE', $d);
            return $d['IMAGE_ID'];
        }
    }
    
    public function del(){
        $id = $_POST['id'];
        $this->area->trans_start();
        
        $area   = $this->area->getArea($id);
        if($area[0]->IMAGE_ID){
            $delImg = $this->area->delete('STY_IMAGE', array('IMAGE_ID' => $area[0]->IMAGE_ID));
        }
        $delArea = $this->area->deleteArea($id);
        if(!empty($area[0]->IMAGE_ID)){
            $this->deleteFile($area[0]->IMAGE_FNAME);
            $this->area->delete('STY_IMAGE', ['IMAGE_ID' => $area[0]->IMAGE_ID]);
        }
        $this->area->trans_complete();
        // $data = $this->area->getAreas();
        $data = $this->dataAreaConvert();
        $res = array(
            'data'   => $data,
            'status' => $delArea
        );
        echo json_encode($res);
    }
}