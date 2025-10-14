
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_file.php';
// require_once APPPATH.'third_party/PHPExcel.php';
class migration extends MY_Controller {
    
    use _File;
    private $title = 'migration-electric';

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        // $this->load->model('gpreport_model', 'gp');
        $this->load->model('type_model', 'type');
        $this->load->model('electric_model', 'elec');
        $this->load->model('form_model', 'frm');
        $this->load->model('user_model', 'usr');
    }
    
    public function index(){
        $data['title'] = $this->title;
        $this->views('electric/migration',$data);
    }

    public function save(){
        $data   = json_decode($this->input->post('data'), true);
        $sheet  = $this->input->post('sheet');
        if(empty($data)) {
            echo json_encode(['message' => 'No data to save', 'status' => 404]);
            return;
        }
        switch($sheet) {
            case 'OFFICE':
                echo json_encode($this->saveElectric($data, 1));
                break;
            case 'FACTORY':
                echo json_encode($this->saveElectric($data, 2));
                break;
            default:
                echo json_encode($this->saveArea($data));
                return;
        }
    }

    /**
     * Insert data to STY_ELECTRIC_MASTER and STY_ELECTRIC_DETAIL with excel file
     * @param array $data
     * @param int $typeNo
     * @return array
     */
    private function saveElectric($data, $typeNo) {
        $category = $this->type->getType(['TYPE_CODE' => 'ET'])[0]->TYPE_ID;
        $type = $this->type->getType(['TYPE_CODE' => 'AT', 'TYPE_NO' => $typeNo])[0]->TYPE_ID;
        $runno = $this->frm->generate_id('STY_FORM_MASTER', 'FRM_NO',['FRM_CATEGORY' => $category]);
        $formDB   = $this->frm->getMaster(['FRM_CATEGORY' => $category, 'FRM_TYPE' => $type]);
        $topicDB  = $this->frm->getTopic(['FRMT_CATEGORY' => $category, 'FRMT_RUNNO' => $runno]);
        $detailDB = $this->frm->getDetail(['FRMD_CATEGORY' => $category, 'FRMD_RUNNO' => $runno]);

        $checkForm = [];
        $checkListTopic = [];
        $existForm = [];
        $existTopic = [];
        $existDetail = [];  
        $form   = [];
        $topic   = [];
        $detail = [];
        foreach ($formDB as $f) {
            $existForm[$f->CATEGORY] = true;
        }
        foreach ($topicDB as $m) {
            $existTopic[$m->FRMT_NO] = true;
        }
        foreach ($detailDB as $d) {
            $existDetail[$d->FRMD_NO] = true;
        }
        // return ['form'    => $existForm,
        //     'master'  => $existTopic,
        //     'detail'  => $existDetail, ];
        foreach($data as $d){
            if(empty($d[0])) continue;
            if (!isset($checkForm[$category]) && !isset($existForm[$category]) ) {
                $checkForm[$category] = true;

                $form[] = [
                    'FRM_CATEGORY'  => $category,
                    'FRM_NO'     => $this->frm->generate_id('STY_FORM_MASTER', 'FRM_NO',['FRM_CATEGORY' => $category]),
                    'FRM_TYPE'   => $type,
                ];
            }
            if (!isset($checkListTopic[$d[0]]) && !isset($existTopic[$d[0]]) && count($form) > 0) { 
                $checkListTopic[$d[0]] = true;
                $topic[] = [
                    'FRMT_CATEGORY' => $category,
                    'FRMT_RUNNO' => $form[0]['FRM_NO'],
                    'FRMT_NO' => $d[0],
                    'FRMT_TOPIC' => isset($d[1]) ? trim($d[1]) : NULL,
                    'FRMT_TOPICEN' => isset($d[2]) ? trim($d[2]) : NULL,
                    'FRMT_USERCREATE' => 0,
                ];
            }
            if (!isset($existDetail[$d[0]]) && count($form) > 0 ) {
                $detail[] = [
                    'FRMD_CATEGORY' => $category,
                    'FRMD_RUNNO' => $form[0]['FRM_NO'],
                    'FRMD_NO' => $d[0],
                    'FRMD_SEQ' => isset($d[5]) ? trim($d[5]) : NULL,
                    'FRMD_DETAIL' => isset($d[3]) ? trim($d[3]) : NULL,
                    'FRMD_DETAILEN' => isset($d[4]) ? trim($d[4]) : NULL,
                    'FRMD_USERCREATE' => 0,
                ];
            }
        }
        // return ['form'    => $form,
        //     'master'  => $topic,
        //     'detail'  => $detail, ];
        $this->elec->trans_start();
        if (!empty($form)) $this->elec->insert_batch('STY_FORM_MASTER', $form);
        if (!empty($topic)) $this->elec->insert_batch('STY_FORM_TOPIC', $topic);
        if (!empty($detail)) $this->elec->insert_batch('STY_FORM_DETAIL', $detail);
        $this->elec->trans_complete();
        $status = $this->elec->trans_status() === FALSE ? 0 : 1;
        return [
            'message' => $status == 1 ? 'Save data success' : 'Save data failed', 
            'status'  => $status,
            'form'    => $form,
            'topic'  => $topic,
            'detail'  => $detail, 
        ];
    }

    private function saveArea($data){
        $area = $this->elec->select('STY_AREA');
        $existArea = [];
        $insert = [];
        $update = [];
        foreach ($area as $a) {
            $existArea[$a->AREA_ID] = true;
        }
        foreach($data as $d){
            if(empty($d[1])) continue;
            if (!isset($existArea[$d[0]]) ) {
                $user = $this->usr->getUser((string)$d[1]);
                $user = !empty($user) ? $user[0] : null;
                $category = $this->type->getType(['TYPE_CODE' => 'ET'])[0]->TYPE_ID;
                $insert[] = [
                    'AREA_ID' => $d[0],
                    'AREA_NAME'       => isset($d[2]) ? trim($d[2]) : NULL,
                    'AREA_ENAME'      => isset($d[2]) ? trim($d[2]) : NULL,
                    'AREA_EMPNO'      => isset($d[1]) ? trim($d[1]) : NULL,
                    'AREA_OWNER'      => isset($user->STNAME)   ? $user->STNAME : NULL,
                    'AREA_MANAGER'      => isset($user->SDEPT)   ? ($user->SDEPT == 'No Department') ? NULL : $user->SDEPT : NULL,
                    'AREA_MANAGERCODE'  => isset($user->SDEPCODE)   ? ($user->SDEPCODE == '00' ? NULL : $user->SDEPCODE) : NULL,
                    'AREA_DIV'        => isset($user->SDIV)     ? $user->SDIV : NULL,
                    'AREA_DIVCODE'    => isset($user->SDIVCODE) ? $user->SDIVCODE : NULL,
                    'AREA_DEPT'       => isset($user->SDEPT)    ? $user->SDEPT : NULL,
                    'AREA_DEPTCODE'   => isset($user->SDEPCODE) ? $user->SDEPCODE : NULL,
                    'AREA_SEC'        => isset($user->SSEC)     ? $user->SSEC : NULL,
                    'AREA_SECCODE'    => isset($user->SSECCODE) ? $user->SSECCODE : NULL,
                    'AREA_TYPE'       => isset($d[3]) ? $this->type->getType(['TYPE_CODE' => 'AT', 'TYPE_NO' => trim($d[3])])[0]->TYPE_ID  : NULL,
                    'AREA_CATEGORY'   => $category,
                    'AREA_USERCREATE' => 0,
                ];
            }else{
                $user = $this->usr->getUser((string)$d[1]);
                $user = !empty($user) ? $user[0] : null;
                $category = $this->type->getType(['TYPE_CODE' => 'ET'])[0]->TYPE_ID;
                $update[] = [
                    'AREA_ID' => $d[0],
                    'AREA_NAME'       => isset($d[2]) ? trim($d[2]) : NULL,
                    'AREA_ENAME'      => isset($d[2]) ? trim($d[2]) : NULL,
                    'AREA_EMPNO'      => isset($d[1]) ? trim($d[1]) : NULL,
                    'AREA_OWNER'      => isset($user->STNAME)   ? $user->STNAME : NULL,
                    'AREA_MANAGER'      => isset($user->SDEPT)   ? ($user->SDEPT == 'No Department') ? NULL : $user->SDEPT : NULL,
                    'AREA_MANAGERCODE'  => isset($user->SDEPCODE)   ? ($user->SDEPCODE == '00' ? NULL : $user->SDEPCODE) : NULL,
                    'AREA_DIV'        => isset($user->SDIV)     ? $user->SDIV : NULL,
                    'AREA_DIVCODE'    => isset($user->SDIVCODE) ? $user->SDIVCODE : NULL,
                    'AREA_DEPT'       => isset($user->SDEPT)    ? $user->SDEPT : NULL,
                    'AREA_DEPTCODE'   => isset($user->SDEPCODE) ? $user->SDEPCODE : NULL,
                    'AREA_SEC'        => isset($user->SSEC)     ? $user->SSEC : NULL,
                    'AREA_SECCODE'    => isset($user->SSECCODE) ? $user->SSECCODE : NULL,
                    'AREA_TYPE'       => isset($d[3]) ? (int)$this->type->getType(['TYPE_CODE' => 'AT', 'TYPE_NO' => trim($d[3])])[0]->TYPE_ID  : NULL,
                    'AREA_CATEGORY'   => (int)$category,
                    'AREA_USERUPDATE' => '0',
                    // 'AREA_DATEUPDATE' => date('j/n/Y H:i:s'),
                ];
            }
        }
        $this->elec->trans_start();
        if (!empty($insert)) $this->elec->insert_batch('STY_AREA', $insert);
        if (!empty($update)) {
            $this->elec->update_batch('STY_AREA', $update, 'AREA_ID');
            foreach ($update as $u) {
                $this->elec->update('STY_AREA', ['AREA_DATEUPDATE' => 'sysdate'], ['AREA_ID' => $u['AREA_ID']]);
            }
        }
        $this->elec->trans_complete();
        $status = $this->elec->trans_status() === FALSE ? 0 : 1;
        return [
            'message' => $status == 1 ? 'Save data success' : 'Save data failed',
            'status'  => $status, 
            'area' => $area,
            'insert' => $insert,
            'update' => $update
        ];
    }
}
    