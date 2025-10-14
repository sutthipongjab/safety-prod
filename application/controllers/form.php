<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_file.php';
require_once APPPATH.'controllers/_safetyForm.php';
class form extends MY_Controller {

    use _File;
    use _safetyForm;

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Form_model', 'frm');
        $this->load->model('User_model', 'usr');
        $this->load->model('Type_model', 'type');
        $this->upload_path = "//amecnas/AMECWEB/File/" .($this->_servername()=='amecweb' ? 'production' : 'development') ."/safety/image/form/";
    }
    
    public function main($title, $formType = ''){
        $formData = $this->type->getType(['TYPE_CODE' => $formType]);
        $formData = !empty($formData) ? (object)['code' => $formData[0]->TYPE_CODE, 'id' => $formData[0]->TYPE_ID] : (object)['code' => '', 'id' => ''];
        $data['title'] = $title;
        $data['formType'] = $formData;
        $data['type']  = $this->type->getType(['TYPE_CODE' => 'AT']);
        $data['category'] = $this->type->getType(['TYPE_NO' => 99]);
        $data['detailType'] = $this->type->getType(['TYPE_CODE' => 'FRMT']);
        $this->views('form/index',$data);
    }

    public function getFormMaster(){
        $formType = $this->input->post('formType');
        $formData = $formType != '' ? $this->frm->getFormMaster(['CATEGORY_CODE' => $formType]) : $this->frm->getFormMaster();
        echo json_encode($formData);
    }

    /**
     * Get form detail based on category and number
     * @param string $category
     * @param string $no
     * @return array
     */
    public function getFormDetail($category = '', $no =''){
        $category = $this->input->post('category');
        $no = $this->input->post('no');
        echo json_encode(['topic' => $this->getDataTopic($category, $no), 'detail' => $this->getDataFormDetail($category, $no)]);
    }

    /**
     * Get form detail based on category and number
     * @param string $category
     * @param string $no
     */
    private function getDataFormDetail($category, $no){
        $detail = $this->frm->getFormDetail([
            'CATEGORY' => $category,
            'FORMNO'   => $no
        ]);
        return $detail;
    }

    /**
     * Get topic data based on category and number
     * @param string $category
     * @param string $no
     */
    private function getDataTopic($category, $no){
        $topic = $this->frm->getTopic([
            'FRMT_CATEGORY' => $category,
            'FRMT_RUNNO'    => $no
        ]);
        return $topic;
    }
    
    /**
     * Get form detail based on category and number
     * @param string $category
     * @param string $no
     */
    private function getDataDetail($category, $no){
        $detail = $this->frm->getDetail([
            'FRMD_CATEGORY' => $category,
            'FRMD_RUNNO'    => $no
        ]);
        return $detail;
    }

    /**
     * Save form data
     * @return void
     */
    public function save(){
        $post = $this->input->post();
        $dataRes = [];
        switch ($post['formtype']) {
            case 'detail':
                $data = [
                    'FRMD_DETAIL'   => trim($post['DETAIL']),
                    'FRMD_DETAILEN' => trim($post['DETAILEN']),
                    'FRMD_TYPE'     => $post['DETAIL_TYPE'],
                ];
                if(!empty($post['SEQ'])){
                    $action = 'update';
                    $seq = $post['SEQ'];
                    $status = $this->frm->update('STY_FORM_DETAIL', $data, ['FRMD_CATEGORY' => $post['CATEGORY'], 'FRMD_RUNNO' => $post['FORMNO'], 'FRMD_NO' => $post['TOPIC_NO'], 'FRMD_SEQ' => $post['SEQ'] ]);
                }else{
                    $action = 'insert';
                    $seq = $this->frm->generate_id('STY_FORM_DETAIL', 'FRMD_SEQ',  ['FRMD_CATEGORY' => $post['CATEGORY'], 'FRMD_RUNNO' => $post['FORMNO']]);
                    // $seq = $this->frm->generate_id('STY_FORM_DETAIL', 'FRMD_SEQ',  ['FRMD_CATEGORY' => $post['CATEGORY'], 'FRMD_RUNNO' => $post['FORMNO'], 'FRMD_NO' => $post['TOPIC_NO']]);
                    $data['FRMD_CATEGORY'] = $post['CATEGORY'];
                    $data['FRMD_RUNNO'] = $post['FORMNO'];
                    $data['FRMD_NO'] = $post['TOPIC_NO'];
                    $data['FRMD_SEQ'] = $seq;
                    $status = $this->frm->insert('STY_FORM_DETAIL',$data);
                }
                $d = $this->frm->getDetail(['FRMD_CATEGORY' => $post['CATEGORY'], 'FRMD_RUNNO' => $post['FORMNO'], 'FRMD_NO' => $post['TOPIC_NO'], 'FRMD_SEQ' => $seq ]);
                if(!empty($d)){
                    $dataRes['act'] = $d[0];
                    // $dataRes['detail'] = $this->frm->getDetail(['FRMD_CATEGORY' => $post['CATEGORY'], 'FRMD_RUNNO' => $post['FORMNO'], 'FRMD_NO' => $post['TOPIC_NO']]);
                    $dataRes['detail'] = $this->frm->getFormDetail(['CATEGORY' => $post['CATEGORY'], 'FORMNO' => $post['FORMNO'], 'TOPIC_NO' => $post['TOPIC_NO']]);

                }
                break;
            case 'topic':
                $data = [
                    'FRMT_TOPIC'    => trim($post['FRMT_TOPIC']),
                    'FRMT_TOPICEN'   => trim($post['FRMT_TOPICEN']),
                ];
                if(!empty($post['FRMT_NO'])){
                    $action = 'update';
                    $no = $post['FRMT_NO'];
                    $status = $this->frm->update('STY_FORM_TOPIC', $data, ['FRMT_CATEGORY' => $post['FRMT_CATEGORY'], 'FRMT_RUNNO' => $post['FRMT_RUNNO'], 'FRMT_NO' => $no]);
                }else{
                    $action = 'insert';
                    $no = $this->frm->generate_id('STY_FORM_TOPIC', 'FRMT_NO', ['FRMT_CATEGORY' => $post['FRMT_CATEGORY'], 'FRMT_RUNNO' => $post['FRMT_RUNNO']]);
                    $data['FRMT_CATEGORY'] = $post['FRMT_CATEGORY'];
                    $data['FRMT_RUNNO'] = $post['FRMT_RUNNO'];
                    $data['FRMT_NO'] = $no;
                    $status = $this->frm->insert('STY_FORM_TOPIC', $data);
                }
                $d = $this->frm->getTopic(['FRMT_CATEGORY' => $post['FRMT_CATEGORY'], 'FRMT_RUNNO' => $post['FRMT_RUNNO'], 'FRMT_NO' => $no]);
                if(!empty($d)){
                    $dataRes = $d[0];
                }
                break;
            default:
                $data = [
                    'FRM_TYPE'     => $post['TYPE'],
                    'FRM_NAME'     => trim($post['FORMNAME']),
                    'FRM_ENAME'    => trim($post['FORMENAME']),
                ];
                if(!empty($post['FORMNO'])){
                    $action = 'update';
                    $no = $post['FORMNO'];
                    $data['FRM_STATUS'] = $post['STATUS'];
                    $status = $this->frm->update('STY_FORM_MASTER', $data, ['FRM_CATEGORY' => $post['CATEGORY'], 'FRM_NO' => $no]);
                }else{
                    $action = 'insert';
                    $no = $this->frm->generate_id('STY_FORM_MASTER', 'FRM_NO', ['FRM_CATEGORY' => $post['CATEGORY']]);
                    $data['FRM_CATEGORY'] = $post['CATEGORY'];
                    $data['FRM_STATUS'] = 1;
                    $data['FRM_NO'] = $no;
                    $status = $this->frm->insert('STY_FORM_MASTER',$data);
                }
                $d = $this->frm->getFormMaster(['CATEGORY' => $post['CATEGORY'], 'FORMNO' => $no]);
                if(!empty($d)){
                    $dataRes = $d[0];
                }
                break;
        }

        $res = [
            'data'   => $dataRes,
            'status' => $status,
            'action' => $action
        ];
        echo json_encode($res);
    }

    /**
     * delete form data
     * @return void
     */
    public function del(){
        $post = $this->input->post();
        switch ($post['formtype']) {
            case 'detail':
                $d = $this->frm->getDetail(['FRMD_CATEGORY' => $post['CATEGORY'], 'FRMD_RUNNO' => $post['FORMNO'], 'FRMD_NO' => $post['no'], 'FRMD_SEQ' => $post['seq'] ]);
                $status = $this->frm->delete('STY_FORM_DETAIL', [
                    'FRMD_CATEGORY' => $post['CATEGORY'],
                    'FRMD_RUNNO'    => $post['FORMNO'], 
                    'FRMD_NO'       => $post['no'],
                    'FRMD_SEQ'      => $post['seq']
                ]);
                if(!empty($d)){
                    $dataRes['act'] = $d[0];
                    $dataRes['detail'] = $this->frm->getFormDetail(['CATEGORY' => $post['CATEGORY'], 'FORMNO' => $post['FORMNO'], 'TOPIC_NO' => $post['no']]);
                }else{
                    $dataRes = [];
                }
                break;
            case 'topic' :
                $this->frm->trans_start();
                $this->frm->delete('STY_FORM_DETAIL', [
                    'FRMD_CATEGORY' => $post['CATEGORY'], 
                    'FRMD_RUNNO'    => $post['FORMNO'],
                    'FRMD_NO'       => $post['no']
                ]);
                $this->frm->delete('STY_FORM_IMAGE', [
                    'FRMI_CATEGORY' => $post['CATEGORY'], 
                    'FRMI_RUNNO'    => $post['FORMNO'],
                    'FRMI_NO'       => $post['no']
                ]);
                $this->frm->delete('STY_FORM_TOPIC', [
                    'FRMT_CATEGORY' => $post['CATEGORY'], 
                    'FRMT_RUNNO'    => $post['FORMNO'], 
                    'FRMT_NO'       => $post['no']]
                );
                $this->frm->trans_complete();
                $status = $this->frm->trans_status() === FALSE ? 0 : 1;
                $dataRes = $status == 1 ? [
                    'FRMT_CATEGORY' => $post['CATEGORY'], 
                    'FRMT_RUNNO'    => $post['FORMNO'], 
                    'FRMT_NO'       => $post['no'
                ]] : [];
                break;
            default:
                $this->frm->trans_start();
                $this->frm->delete('STY_FORM_DETAIL', [
                    'FRMD_CATEGORY' => $post['CATEGORY'], 
                    'FRMD_RUNNO'    => $post['FORMNO']
                ]);
                $this->frm->delete('STY_FORM_IMAGE', [
                    'FRMI_CATEGORY' => $post['CATEGORY'], 
                    'FRMI_RUNNO'    => $post['FORMNO'],
                ]);
                $this->frm->delete('STY_FORM_TOPIC', [
                    'FRMT_CATEGORY' => $post['CATEGORY'], 
                    'FRMT_RUNNO'    => $post['FORMNO']
                ]);
                $this->frm->delete('STY_FORM_MASTER', [
                    'FRM_CATEGORY' => $post['CATEGORY'], 
                    'FRM_NO'       => $post['FORMNO']
                ]);
                $this->frm->trans_complete();
                $status = $this->frm->trans_status() === FALSE ? 0 : 1;
                $dataRes = $status == 1 ? [
                    'CATEGORY' => $post['CATEGORY'], 
                    'FORMNO'   => $post['FORMNO']
                ] : [];
                break;
        }
        $res = [
            'data'   => $dataRes,
            'status' => $status,
            'action' => 'delete'
        ];
        echo json_encode($res);
    }

    public function getImage($category = '', $runno = '', $no = ''){
        if($category == '' && $runno == '' && $no == ''){
            $post = $this->input->post();
            $data = $this->frm->getFormImage(['FRMI_CATEGORY' => $post['FRMT_CATEGORY'], 'FRMI_RUNNO' => $post['FRMT_RUNNO'], 'FRMI_NO' => $post['FRMT_NO']]);
            echo json_encode($this->convImg($data));
        }else{
            $data = $this->frm->getFormImage(['FRMI_CATEGORY' => $category, 'FRMI_RUNNO' => $runno, 'FRMI_NO' => $no]);
            return $this->convImg($data);
        }
        
        // var_dump($data);
    }

    /**
     * Convert image to base64
     * @param array $data 
     * @return array
     */
    private function convImg($data){
        if(!empty($data)){
            foreach($data as $d){
                $d->base64 = $this->conVBase64($d->FRMI_PATH.$d->FRMI_FNAME);
            }
        }
        return $data;
    }

    public function addImage(){
        $post = $this->input->post();
        $this->frm->trans_start();
        if(isset($_FILES['FancyImage'])){
            $files       = $_FILES['FancyImage'];
            $total_files = count($files['name']); // นับไฟล์
            for ($i = 0; $i < $total_files; $i++) {
                // สร้างอาร์เรย์ใหม่เพื่อเก็บไฟล์แต่ละไฟล์
                $file = array(
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i]
                );
    
                // อัปโหลดไฟล์
                $uploadedFile = $this->uploadFile($file);
                
                // ตรวจสอบว่าอัปโหลดสำเร็จหรือไม่
                if ($uploadedFile['status'] === 'success') {
                    $this->setImageForm($uploadedFile, $post);
                }
            }
        }
        $this->frm->trans_complete();
        $status = $this->frm->trans_status() === FALSE ? 0 : 1;
        $res = [
            'data'   => $this->getImage($post['FRMI_CATEGORY'], $post['FRMI_RUNNO'], $post['FRMI_NO']),
            'status' => $status
        ];
        echo json_encode($res);
    }

    public function deleteImage(){
        $post = $this->input->post();
        $cond = [
            'FRMI_CATEGORY' => $post['FRMI_CATEGORY'],
            'FRMI_RUNNO'    => $post['FRMI_RUNNO'],
            'FRMI_NO'       => $post['FRMI_NO'],
            'FRMI_ID'       => $post['FRMI_ID'],
        ];
        $status = $this->frm->delete('STY_FORM_IMAGE',$cond);
        $res = [
            'data'  => $this->getImage($post['FRMI_CATEGORY'], $post['FRMI_RUNNO'], $post['FRMI_NO']),
            'status' => $status
        ];
        echo json_encode($res);
    }

    // public function getForm(){
    //     $post = $this->input->post();
    //     $form = $this->frm->getFormMaster(['CATEGORY' => $post['areaCategory'], 'TYPE' => $post['areaType']]);
    //     $res  = [];
    //     if(!empty($form)){
    //         $form = $form[0];
    //         $res  = $this->setForm($post['areaCategory'], $form->FORMNO);
    //     }
    //     echo json_encode($res);
    // }

    // /**
    //  * Set form 
    //  * @param string $category e.g. 25
    //  * @param string $runno    e.g. 1
    //  * @return array
    //  */
    // private function setForm($category, $runno){
    //     $topic  = $this->frm->getTopic(['FRMT_CATEGORY' => $category, 'FRMT_RUNNO' => $runno]);
    //     // $detail = $this->frm->getDetail(['FRMD_CATEGORY' => $category, 'FRMD_RUNNO' => $runno]);
    //     $detail = $this->frm->getFormDetail(['CATEGORY' => $category, 'RUNNO' => $runno]);
    //     $image  = $this->frm->getFormImage(['FRMI_CATEGORY' => $category, 'FRMI_RUNNO' => $runno]);
    //     $res = [];
    //     if(!empty($topic)){
    //         foreach($topic as $key => $t){
    //             $res[$key] = $t; 
    //             $res[$key]->image = [];
    //             $res[$key]->detail = [];
    //             if(!empty($detail)){
    //                 foreach ($detail as $d) {
    //                     // if($d->FRMD_NO == $t->FRMT_NO){
    //                     if($d->TOPIC_NO == $t->FRMT_NO){
    //                         $res[$key]->detail[] = $d;
    //                     }
    //                 }
    //             }
    //             if(!empty($image)){
    //                 $index = 0;
    //                 foreach ($image as $k => $i) {
    //                     if($i->FRMI_NO == $t->FRMT_NO){
    //                         $res[$key]->image[$index] = $i;
    //                         $res[$key]->image[$index]->base64 = $this->conVBase64($i->FRMI_PATH.$i->FRMI_FNAME);
    //                         $index++;
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     return $res;
    // }

}