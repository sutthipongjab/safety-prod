
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_file.php';
class request extends MY_Controller {

    use _FILE;
    private $title = 'request-chemical';

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('chemical_model', 'che');
        $this->load->model('Webform_model', 'wf');
        $this->load->model('user_model', 'usr');
        $this->upload_path = "//amecnas/AMECWEB/File/" .($this->_servername()=='amecweb' ? 'production' : 'development') ."/safety/files/Chemical/";
    }

    private function getPositionCode($vorgno) {
        if (substr($vorgno, 2, 2) == '01') {
            return 'SDIVCODE';
        } elseif (substr($vorgno, 4, 2) == '01') {
            return 'SDEPCODE';
        } else {
            return 'SSECCODE';
        }
    }
    
    public function index(){
        // var_dump($_SESSION);
        $data['title'] = $this->title;
        $data['formInfo'] = $this->wf->getFormMaster('ST-CHM');
        $this->views('chemical/request',$data);
    }

    public function getChemical(){
        $empno = $this->input->post('empno');
        $userData = $this->usr->getUser($empno);
        $user = !empty($userData) ? $userData[0] : null;
        $data  = $this->che->getChemical();
        $org   = $this->usr->getOwnOrg($empno);
        foreach($data as $key => $d){
            $d->name = '('.$d->AMEC_SDS_ID.')'.$d->CHEMICAL_NAME;
        }
        $orgAll = [];
        $dept = [];
        if(!empty($org)){
            foreach ($org as $key => $o) {
                $pos = $this->getPositionCode($o->VORGNO);
                $dept = array_merge($dept, $this->usr->getOrg(array($pos => $o->VORGNO)));
                // if(!empty($dept)){
                //     foreach ($dept as $key => $d) {
                //         $orgAll['name'][] = strtoupper($d->SSEC);
                //         $orgAll['code'][] = strtoupper($d->SSECCODE);
                //     }
                // }
            }
            // $orgAll['name'] = array_unique($orgAll['name']);
            // $orgAll['code'] = array_unique($orgAll['code']);
        }
        $res = array(
            'data' => $data, 
            'user' => $user,
            'org' => $dept
            // 'org' => $orgAll,
        );   
        echo json_encode($res);
    }

    public function searchChe(){
        $id   = $this->input->post('id');
        $owner  = $this->input->post('owner');
        $empno  = $this->input->post('empno');
        
        $cond = array(
            'AMEC_SDS_ID' => $id,
            'OWNERCODE'   => $owner
        );
        $data = $this->che->getCHMSec($cond);
        
        if(empty($data)){
            $userData = $this->usr->getUser($empno);
            $user = !empty($userData) ? $userData[0] : null;
            if($user){
                // $data = $this->che->getCHMSec(array('AMEC_SDS_ID' => $id, "REPLACE(\"OWNER\", ' ', '')".'=' => str_replace(' ','',$user->SDEPT)));
                $data = $this->che->getCHMSec(array('AMEC_SDS_ID' => $id, 'OWNERCODE' => $user->SDEPCODE));
            }
        }
        $res = array('data' => $data, 'status' => !empty($data) ? 1 : 0);
        echo json_encode($res);
    }

    public function createForm(){
        try {

            $AMEC_SDS_ID        = $this->input->post('AMEC_SDS_ID');
            $EFFECTIVE_DATE     = $this->input->post('EFFECTIVE_DATE');
            $CHEMICAL_NAME      = $this->input->post('CHEMICAL_NAME');
            $QUANTITY_KG        = $this->input->post('QUANTITY_KG');
            $QUANTITY_TYPE      = $this->input->post('QUANTITY_TYPE');
            $USED_FOR           = $this->input->post('USED_FOR');
            $USED_AREA          = $this->input->post('USED_AREA[]');
            $KEEPING_POINT      = $this->input->post('KEEPING_POINT[]');
            // $KEEPING_POINT_FILE = $this->input->post('KEEPING_POINT_FILE');
            $QTY                = $this->input->post('QTY');
            $userno             = $this->input->post('userno');
            $newChemical        = $this->input->post('newChemical');
            $ownercode          = $this->input->post('ownercode');

            $NFRMNO             = $this->input->post('formtype');
            $VORGNO             = $this->input->post('owner');
            $CYEAR              = $this->input->post('cyear');
            $CYEAR2             = $this->input->post('cyear2');
            $NRUNNO             = $this->input->post('runno');

            $area = '';
            $keep = '';

            $position = $this->getPositionCode($ownercode);
            $owner = $this->usr->getOrg(array($position => $ownercode));

            $data = array(
                'NFRMNO'            => $NFRMNO,
                'VORGNO'            => $VORGNO,
                'CYEAR'             => $CYEAR,
                'CYEAR2'            => $CYEAR2,
                'NRUNNO'            => $NRUNNO,
                'OWNER'             => !empty($this->che->getOwnerByCode($ownercode)) 
                                    ? $this->che->getOwnerByCode($ownercode)[0]->OWNER 
                                    : ($owner[0]->SSECCODE == '00' ? $owner[0]->SDEPT : $owner[0]->SSEC),
                'OWNERCODE'         => $ownercode,
                // 'AMEC_SDS_ID'       => $AMEC_SDS_ID,
                // 'EFFECTIVE_DATE'    => $EFFECTIVE_DATE,
                'CHEMICAL_NAME'     => $CHEMICAL_NAME,
                'QUANTITY_KG'       => $QUANTITY_KG,
                'QUANTITY_TYPE'     => $QUANTITY_TYPE,
                'USED_FOR'          => $USED_FOR,
                // 'USED_AREA'         => $USED_AREA,
                // 'KEEPING_POINT'     => $KEEPING_POINT,
                // 'KEEPING_POINT_FILE'=> $KEEPING_POINT_FILE,
                'QTY'               => $QTY,
                'USER_CREATE'       => $userno,
                'NEW_CHEMICAL'      => $newChemical,
                'FORM_TYPE'         => 1
            );
            $this->che->trans_start();

            if(isset($_FILES['KEEPING_POINT_FILE'])){
                $file = $this->uploadfile($_FILES['KEEPING_POINT_FILE']);
                if($file['status'] == 'success'){
                    $data['LAYOUT_FILE'] = $this->setFile($file, $userno, 'CM');
                }
            }

            foreach($USED_AREA as $key => $a){
                $area .= $a.'|';
            }
            $data['USED_AREA'] = rtrim($area,'|');

            foreach($KEEPING_POINT as $key => $a){
                $keep .= $a.'|';
            }
            $data['KEEPING_POINT'] = rtrim($keep, '|');

            if($AMEC_SDS_ID){
                $data['AMEC_SDS_ID']    = $AMEC_SDS_ID;
                $data['EFFECTIVE_DATE'] = $this->conVdateToDB($EFFECTIVE_DATE);
            }
            // else{
            //     $data['AMEC_SDS_ID'] = $this->type->generate_id('STY_CHEMICAL','AMEC_SDS_ID');
            // }

            $this->che->insert('STY_CHEMICAL_FORM', $data);

            if(!$newChemical){
                $form = array(
                    'NFRMNO' => $NFRMNO,
                    'VORGNO' => $VORGNO,
                    'CYEAR' => $CYEAR,
                    'CYEAR2' => $CYEAR2,
                    'NRUNNO' => $NRUNNO,
                );
                $this->updateFlow($form);
            }


            $this->che->trans_complete(); // ensuring to complete the transaction
            if ($this->che->trans_status() === FALSE) {
                $status = false;
            } else {
                $status = true;
            }
            $res = array(
                'data'   => $data,
                'status' => $status
            );  
            echo json_encode($res);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }

    }

    
    /**
     * Update flow.
     * @param array $form The form number.
     */
    private function updateFlow($form){
    // public function updateFlow(){
        $condition = array(
            // 'NFRMNO'  => 1,
            // 'VORGNO'  => '020601',
            // 'CYEAR'   => '25',
            // 'CYEAR2'  => '2025',
            // 'NRUNNO'  => 1,
            'NFRMNO'  => $form['NFRMNO'],
            'VORGNO'  => $form['VORGNO'],
            'CYEAR'   => $form['CYEAR'],
            'CYEAR2'  => $form['CYEAR2'],
            'NRUNNO'  => $form['NRUNNO'],
        );

        $this->wf->deleteExtra($condition, ['04', '05', '06']);
        $condition['CEXTDATA'] = '07';
        $data = array(
            'CSTEPNEXTNO' => $this->wf->getCSETPNO($condition)[0]->CSTEPNO
        ); 
        $condition['CEXTDATA'] = '03';
        $this->wf->update('FLOW', $data, $condition); 
    }

    // /**
    //  * Set file
    //  * @param array $data
    //  * @param string $userno
    //  * @return string
    //  */
    // private function setFile($data, $userno){
    //     $this->load->model('Type_model', 'type');
    //     $d = array(
    //         'FILE_ID'    => $this->type->generate_id('STY_FILES', 'FILE_ID'),
    //         'FILE_ONAME' => $data['file_origin_name'],
    //         'FILE_FNAME' => $data['file_name'],
    //         'TYPE_ID'    => $this->type->getTypeByCode('CM')[0]->TYPE_ID,
    //         'FILE_PATH'  => $this->upload_path,
    //         'FILE_USERCREATE' => $userno
    //     );
    //     $this->che->insert('STY_FILES', $d);
    //     return $d['FILE_ID'];
    // }



}