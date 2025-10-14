<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_file.php';
class inspection extends MY_Controller {

    use _File;
    private $title = 'inspection';

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('Inspection_model', 'ins');
        $this->load->model('Area_model', 'area');
        $this->load->model('Webform_model', 'wf');
        $this->upload_path = "//amecnas/AMECWEB/File/" .($this->_servername()=='amecweb' ? 'production' : 'development') ."/safety/image/Patrol/";
    }
    
    public function index(){
        $data['title']   = $this->title;
        $this->views('inspection/patrol',$data);
    }

    /**
     * Get all patrols.
     * @return array The patrols data.
     */
    public function getPatrols(){
        $monthYear = $_POST['monthYear'];
        $start = date('j/n/Y', strtotime($monthYear));
        $end = date('t/n/Y', strtotime($monthYear));
        // echo $start.' '.$end;
        $data = $this->ins->getPatrols($start, $end);
        if(!empty($data)){
            foreach($data as $key => $d){
                $data[$key]->PA_DATE = date('j/n/Y', strtotime($d->PA_DATE));
            }
        }
        echo json_encode($data);
    }

    /**
     * Get patrol detail.
     * @param string $NFRMNO The form number.
     * @param string $VORGNO The organization number.
     * @param string $CYEAR The year.
     * @param string $CYEAR2 The year2.
     * @param string $NRUNNO The run number.
     */
    public function detail($NFRMNO, $VORGNO, $CYEAR, $CYEAR2, $NRUNNO){
        // $patrol          = $this->getPatrol($NFRMNO, $VORGNO, $CYEAR, $CYEAR2, $NRUNNO);
        $form = array(
            'NFRMNO' => $NFRMNO,
            'VORGNO' => $VORGNO,
            'CYEAR'  => $CYEAR,
            'CYEAR2' => $CYEAR2,
            'NRUNNO' => $NRUNNO
        );
        $data['form']    = $form;
        $data['website'] = 'safety';
        $data['title']   = $this->title;
        // $data['patrol']  = $patrol['data'];
        // $data['flow']    = $this->amecform->showFlow($nfrmno, $vorgno, $cyear, $cyear2, $nrunno, '', '', 'remark');	
        $this->views('inspection/inspectionForm',$data);
    }

     /**
     * Get patrol.
     * @param string $NFRMNO The form number.
     * @param string $VORGNO The organization number.
     * @param string $CYEAR The year.
     * @param string $CYEAR2 The year2.
     * @param string $NRUNNO The run number.
     * @return array The patrol data.
     */
    public function getPatrol($NFRMNO, $VORGNO, $CYEAR, $CYEAR2, $NRUNNO){
        $form = array(
            'NFRMNO' => $NFRMNO,
            'VORGNO' => $VORGNO,
            'CYEAR'  => $CYEAR,
            'CYEAR2' => $CYEAR2,
            'NRUNNO' => $NRUNNO
        );
        $data = $this->ins->getPatrol($form);
        if(!empty($data)){
            foreach($data as $key => $d){
                $data[$key]->PA_DATE = date('Y-m-d', strtotime($d->PA_DATE));
                $data[$key]->baseURL = $this->conVBase64($data[$key]->IMAGE_PATH.$data[$key]->IMAGE_FNAME);
            }
        }
        $res = array(
            'data' => $data,
            'form' => $form
        );
        echo json_encode($res);
    }

    /**
     * Create patrol.
     */
    public function createPatrol(){
        $data['title']   = 'create-patrol';
        $data['section'] = $this->ins->getAllSection();
        $data['st']      = $this->ins->getST();
        $data['area']    = $this->area->getAreas(); 
        $data['cla']     = $this->ins->getClass('PTC');
        $data['items']   = $this->ins->getItems();
        $data['formInfo'] = $this->wf->getFormMaster('ST-INP');
        $this->views('inspection/inspection',$data);
    }

    /**
     * Get owner section.
     */
    public function getSem(){
        $seccode  = $_POST['seccode'];
        if($seccode == '020601'){ //st dept
            $data = $this->ins->getSem($seccode, '21');
        }else{
            $data = $this->ins->getSem($seccode);
        }
            
        echo json_encode($data);
    }

    /**
     * Save data
     */
    public function save(){
        try {
            $status = false;
            $patrol = array();
            if(!empty($_POST['formtype'])){
                if (isset($_POST['data'])) {
                    $data = isset($_POST['data']) ? json_decode($_POST['data'], true) : []; // แปลง JSON string เป็น array
                } 
                $userno = $_POST['userno'];
                $NFRMNO = $_POST['formtype'];
                $VORGNO = $_POST['owner'];
                $CYEAR  = $_POST['cyear'];
                $CYEAR2 = $_POST['cyear2'];
                $NRUNNO = $_POST['runno'];
                $PA_OWNER = $_POST['PA_OWNER'];
                
                $dataPatrol = array(
                    'NFRMNO'        => $NFRMNO,
                    'VORGNO'        => $VORGNO,
                    'CYEAR'         => $CYEAR,
                    'CYEAR2'        => $CYEAR2,
                    'NRUNNO'        => $NRUNNO,
                    'PA_OWNER'      => $PA_OWNER,
                    'PA_DATE'       => date('j/n/Y',strtotime($_POST['PA_DATE'])),
                    'PA_SECTION'    => $_POST['PA_SEC'],
                    'PA_AUDIT'	  	=> $_POST['PA_AUDIT'],
                    'PA_USERCREATE'	=> $userno
                );
                

                $this->ins->trans_start();
                foreach($data as $key => $d){
                    $dataPatrol['PA_ID']         = $key+1;
                    $dataPatrol['PA_ITEMS']      = $d['PA_ITEMS'];
                    $dataPatrol['PA_AREA']       = $d['PA_AREA'];
                    $dataPatrol['PA_DETECTED']   = $d['PA_DETECTED'];
                    $dataPatrol['PA_CLASS']      = $d['PA_CLASS'];
                    $dataPatrol['PA_SUGGESTION'] = $d['PA_SUGGESTION'];
                    $dataPatrol['PA_MAT']        = $d['PA_MAT'];
                    
                    if(isset($_FILES['PA_IMAGE']['name'][$key])){
                        $file = array(
                            'name' => $_FILES['PA_IMAGE']['name'][$key],
                            'type' => $_FILES['PA_IMAGE']['type'][$key],
                            'tmp_name' => $_FILES['PA_IMAGE']['tmp_name'][$key],
                            'error' => $_FILES['PA_IMAGE']['error'][$key],
                            'size' => $_FILES['PA_IMAGE']['size'][$key]
                        );
                        $upload = $this->uploadfile($file);
                        if($upload['status'] == 'success'){
                            $dataPatrol['PA_IMAGE'] = $this->setImage($upload, $userno, 'PT');
                        }
                    }
                    $res = $this->ins->insert('STY_PATROL',$dataPatrol);
                }
                $this->ins->trans_complete();
                if ($this->ins->trans_status() === FALSE) {
                    $status = false;
                } else {
                    $status = true;
                    $dataSet = array(
                        'VAPVNO' => $PA_OWNER,
                        'VREPNO' => $PA_OWNER
                    );
                    $this->updateFlow($NFRMNO, $VORGNO, $CYEAR, $CYEAR2, $NRUNNO, $dataSet, array('06','19'));
                    $form = array(
                        'NFRMNO' => $NFRMNO,
                        'VORGNO' => $VORGNO,
                        'CYEAR'  => $CYEAR,
                        'CYEAR2' => $CYEAR2,
                        'NRUNNO' => $NRUNNO
                    );
                    $patrol = $this->ins->getPatrol($form);
                }
            }
            $res = array(
                'status'  => $status,
                'data'    => !empty($patrol) ? $patrol : [], 
                'form'    => !empty($patrol) ? $form : [],
                'message' => $status ? 'Save data successfully.' : 'Failed to save data.',
            );
            echo json_encode($res);
            // echo json_encode(array('status' => $status));

        } catch (Exception $e) {
            echo json_encode(['status' => 0]);

            // $data = array(
            //     'SUBJECT' => 'Safety Error 😭',
            //     'BODY'    => array($e->getMessage())
            // );
            // try {
            //     $this->mail->sendmail($data);
            //     $this->output->set_status_header(200);
            //     echo json_encode(['status' => 0]);
            // } catch (Exception $mailException) {
            //     log_message('error', 'Failed to send email: ' . $mailException->getMessage());
            // }
        }
    }

    /**
     * Update flow.
     * @param string $NFRMNO The form number.
     * @param string $VORGNO The organization number.
     * @param string $CYEAR The year.
     * @param string $CYEAR2 The year2.
     * @param string $NRUNNO The run number.
     * @param string $empno The employee number.
     */
    private function updateFlow($NFRMNO, $VORGNO, $CYEAR, $CYEAR2, $NRUNNO, $data, $CSTEPNO){
        $condition = array(
            'NFRMNO'  => $NFRMNO,
            'VORGNO'  => $VORGNO,
            'CYEAR'   => $CYEAR,
            'CYEAR2'  => $CYEAR2,
            'NRUNNO'  => $NRUNNO,
            'CSTEPNO' => $CSTEPNO
        );
        $a = $this->wf->update('FLOW', $data, $condition);
    }

    // /**
    //  * Insert STY_IMAGE.
    //  *
    //  * @param array $data The data image.
    //  * @param int $userno The user number.
    //  * @return int The image id.
    //  */
    // private function setImage($data, $userno) {
    //     $d = array(
    //         'IMAGE_ID'    => $this->area->generate_id('STY_IMAGE', 'IMAGE_ID'),
    //         'IMAGE_ONAME' => $data['file_origin_name'],
    //         'IMAGE_FNAME' => $data['file_name'],
    //         'TYPE_ID'     => $this->ins->getTypeByCode('PT')[0]->TYPE_ID,
    //         'IMAGE_PATH'  => $this->upload_path,
    //         'IMAGE_USERCREATE' => $userno
    //     );
    //     $this->area->insert('STY_IMAGE', $d);
    //     return $d['IMAGE_ID'];
    // }
}