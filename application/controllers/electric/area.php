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
    

    public function index(){
        $data['title'] = 'electric-area';
        // $data['section'] = $this->usr->getSectionAll();
        // $data['dept']    = $this->usr->getDeptAll();
        $data['type'] = $this->type->getTypeByCode('AT');
        $this->views('electric/area',$data);
    }

    public function getArea(){
        // $deptCode = $this->input->post('deptCode');
        $post = $this->input->post();
        $area = $this->dataAreaConvert($post);
        echo json_encode($area);
    }

    public function getUserdata(){
        $empno    = $this->input->post('empno');
        $organize = $this->area->getUserdata($empno);
        echo json_encode($organize);
    }

    private function dataAreaConvert($data){
        // $mimeType = 'image/png';
        // $code = ['AREA_CATEGORY_CODE' => 'ET'];
        // $own  = $data['deptCode'] == '00' ? ['AREA_DIVCODE' => $data['divCode']] : ['AREA_DEPTCODE' => $data['deptCode']];
        // $cond = array_merge($code, $own);
        // var_dump($cond);
        $area = $this->area->getAreas(['AREA_MANAGERCODE' => $data['AREA_DEPTCODE'] == '00' ? $data['AREA_DIVCODE'] : $data['AREA_DEPTCODE'], 'AREA_CATEGORY_CODE' => 'ET']);
        foreach ($area as $key => $a) {
            # code...
            $a->baseURL =$this->conVBase64($a->IMAGE_FILE);
            // $a->baseURL = !empty($a->IMAGE_FILE) ? 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($a->IMAGE_FILE)) : '';
        }
        return $area;
    }

    public function save(){
        $msg = '';
        $post = $this->input->post();
        $userno = $post['userno'];
        $id     = $post['AREA_ID'];
        $this->area->trans_start();

        $data = array(
            'AREA_ID'         => $id,
            'AREA_EMPNO'      => $post['AREA_EMPNO'],
            'AREA_OWNER'      => $post['AREA_OWNER'],
            'AREA_DIV'        => $post['AREA_DIV'],
            'AREA_DIVCODE'    => $post['AREA_DIVCODE'],
            'AREA_DEPT'       => $post['AREA_DEPT'],
            'AREA_DEPTCODE'   => $post['AREA_DEPTCODE'],
            'AREA_SEC'        => $post['AREA_SEC'],
            'AREA_SECCODE'    => $post['AREA_SECCODE'],
        );

        if(isset($_FILES['AREA_IMAGE'])){
            $file = $this->uploadfile($_FILES['AREA_IMAGE']);
            if($file['status'] == 'success'){
                $data['AREA_IMAGE'] = $this->setImageDel($file, $userno, $id);
            }else{
                if($post['deleteImage'] == 1){
                    $area = $this->area->getArea($id);
                    if(!empty($area[0]->IMAGE_ID)){
                        $this->deleteFile($area[0]->IMAGE_FNAME);
                        $this->area->delete('STY_IMAGE', ['IMAGE_ID' => $area[0]->IMAGE_ID]);
                        $data['AREA_IMAGE'] = null;
                    }
                }
            }
        }
        $result = $this->area->upsertArea($data, $userno);
        $res = array(
            'data'   => $this->dataAreaConvert($post),
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
}