<?php
defined('BASEPATH') or exit('No direct script access allowed');

trait _File{
    
    public function downloadFile($filename, $oldName){
        $this->load->helper('download');
        $path = $this->upload_path."/";
        $data = file_get_contents($path.$oldName);
        force_download($filename, $data);
    }

    /**
     * Download excel template for Export excel file from exceljs
     * @author Sutthipong Tangmongkhoncharoen
     * @since 2024-10-2
     */
    public function getArrayBufferFile(){
        $filename = $_POST['filename'];
        // $file = FCPATH . "assets/file/template/" .$filename; 
        $filePath = $_POST['filePath']; // assets/file/template/
        $file = FCPATH.$filePath.$filename; 
        // var_dump($file);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Length: ' . filesize($file));

        readfile($file); // ส่งไฟล์ไปยัง client
        exit();
    }

    /**
     * Get excel file in path and convert to base64
     * @param string $path e.g. "assets/file/master/chemical"
     */
    public function getfileInPath(){
        $path      = $_POST['path'];
        $fileName  = isset($_POST['fileName']) ? $_POST['fileName'] : '';
        $directory = FCPATH.$path;
        $files = scandir($directory);
        $response = [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'xlsx' && !(substr($file, 0, 2)=='~$')) {
                $filePath = $directory . DIRECTORY_SEPARATOR . $file;
                $fileContent = file_get_contents($filePath);
                if ($fileName != '') {
                    if($fileName == $file){
                        $response[] = [
                            'filename' => $file,
                            'content' => base64_encode($fileContent)
                        ];
                    }
                }else{
                    $response[] = [
                        'filename' => $file,
                        'content' => base64_encode($fileContent)
                    ];
                }
            }
        }
        echo json_encode($response);
        // header('Content-Type: application/json');
            // $path = "//amecnas/IS_Dept/project/Annual Development/FY2024/Form1/GP/ST/Chemical";
            // $fileName = $this->getExcelFile($path);

            // foreach ($fileName as $key => $f) {
            //     // echo $f;
            //     // readfile($path.'/'.$f);
            // }
            // $res = array(
            //     'path' => $path,
            //     'filename' => $fileName
            // );
            // echo json_encode($res);
    }

    /**
     * Get excel file in path
     * @param string $path e.g. "//amecnas/IS_Dept/project/Annual Development/FY2024/Form1/GP/ST/Chemical"
     */
    private function getExcelFile($path){
        $res = array();
        if(is_dir($path)){
            $d = scandir($path);
            foreach($d as $v){
                if(((substr($v,-5)=='.xlsx') || (substr($v,-4)=='.xls'))&& !(substr($v, 0, 2)=='~$')){
                    $res[] = $v;
                }
            }
        }
        return $res;
    }

    /**
     * Upload file
     * @param array $file 
     */
    private function uploadfile($files){
        $status = '';
        $msg  = '';
        $name = '';
        $size = '';
        $type = '';
        $dt   = date('YndHi');
        
        if(!(is_dir($this->upload_path))) mkdir($this->upload_path, 0777, true);

        $oriName = $files['name'];
        
        $_FILES['file']['name']     = $dt.'_'.$oriName;
        $_FILES['file']['type']     = $files['type'];
        $_FILES['file']['tmp_name'] = $files['tmp_name'];
        $_FILES['file']['error']    = $files['error'];
        $_FILES['file']['size']     = $files['size'];

        $config['upload_path']   = $this->upload_path;
        $config['allowed_types'] = '*';
        $config['max_size'] = 1024*8;
        
        $this->load->library('upload', $config);
        if($this->upload->do_upload('file')){
            $data = $this->upload->data();
            $image_path = $data['full_path'];
            if(file_exists($image_path)){
                $name   = $data['file_name'];
                $oname  = $data['orig_name'];
                $size   = $data['file_size'];
                $type   = $data['file_ext'];
                $status = 'success';
                $msg    = 'File successfully uploaded';
            }else{
                $status = 'error';
                $msg = 'Something went wrong when saving the file, please try again.';
            }
        }else{
            $status = 'error';
            $msg = $this->upload->display_errors('', '');
        }
        return array(
                'status' => $status,
                'msg'    => $msg,
                'file_origin_name' => $oriName,
                'file_name' => $name,
                'file_size' => $size,
                'file_type' => $type,
        );
    }

    /**
     * Set file
     * @param array $data
     * @param string $userno
     * @return string
     */
    private function setFile($data, $userno, $code){
        $this->load->model('Type_model', 'type');
        $d = array(
            'FILE_ID'    => $this->type->generate_id('STY_FILES', 'FILE_ID'),
            'FILE_ONAME' => $data['file_origin_name'],
            'FILE_FNAME' => $data['file_name'],
            'TYPE_ID'    => $this->type->getTypeByCode($code)[0]->TYPE_ID,
            'FILE_PATH'  => $this->upload_path,
            'FILE_USERCREATE' => $userno
        );
        // return $d;
        $this->type->insert('STY_FILES', $d);
        return $d['FILE_ID'];
    }

     /**
     * Insert STY_IMAGE.
     *
     * @param array $data The data image.
     * @param int $userno The user number.
     * @return int The image id.
     */
    private function setImage($data, $userno, $code) {
        $this->load->model('Type_model', 'type');
        $d = array(
            'IMAGE_ID'    => $this->type->generate_id('STY_IMAGE', 'IMAGE_ID'),
            'IMAGE_ONAME' => $data['file_origin_name'],
            'IMAGE_FNAME' => $data['file_name'],
            'TYPE_ID'     => $this->type->getTypeByCode($code)[0]->TYPE_ID,
            'IMAGE_PATH'  => $this->upload_path,
            'IMAGE_USERCREATE' => $userno
        );
        $this->type->insert('STY_IMAGE', $d);
        return $d['IMAGE_ID'];
    }

      /**
     * Insert STY_FORM_IMAGE.
     *
     * @param array $data The data image.
     * @param int $userno The user number.
     * @return int The image id.
     */
    private function setImageForm($file, $data) {
        $this->load->model('Type_model', 'type');
        $d = array(
            'FRMI_CATEGORY'   => $data['FRMI_CATEGORY'],
            'FRMI_RUNNO'      => $data['FRMI_RUNNO'],
            'FRMI_NO'         => $data['FRMI_NO'],
            'FRMI_ID'        => $this->type->generate_id('STY_FORM_IMAGE', 'FRMI_ID'),
            'FRMI_ONAME'      => $file['file_origin_name'],
            'FRMI_FNAME'      => $file['file_name'],
            'FRMI_PATH'       => $this->upload_path,
            'FRMI_USERCREATE' => $data['userno']
        );
        $this->type->insert('STY_FORM_IMAGE', $d);
    }

    /**
     * Delete file
     * @param string $filename 
     */
    private function deleteFile($filename) {
        $filePath = $this->upload_path.$filename;
        if (file_exists($filePath) && !empty($filename)) {
            @unlink($filePath);
        } 
    }
    
}