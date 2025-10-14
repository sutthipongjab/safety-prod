<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/_form.php';
class Webflow extends MY_Controller
{

    use _Form;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ppe_model', 'ppe');
        $this->load->model('webform_model', 'wf');
        // $this->session_expire();
        // Load necessary models, libraries, etc.
    }

    public function index()
    {
        // Default method for the controller
        $data['fmst']     = $fmst = $this->wf->getFormMaster('ST-PPE');
        $nfrmno           = $fmst[0]->NNO;
        $vorgno           = $fmst[0]->VORGNO;
        $cyear            = $fmst[0]->CYEAR;
        $cyear2           = $this->input->get('y2');
        $nrunno           = $this->input->get('runNo');
        $data['empno']    = $empno = $this->input->get('empno');
        $data['edit']     = $this->input->get('edit');
        $data['arr_edit'] = ['95025', '13249'];

        // $data['flow'] = $this->showflow($nfrmno, $vorgno, $cyear, $cyear2, $nrunno);
        $data['frmno'] = $this->toFormNumber($nfrmno, $vorgno, $cyear, $cyear2, $nrunno);
        $data['mode']  = $this->getMode($nfrmno, $vorgno, $cyear, $cyear2, $nrunno, $empno);
        $data['data']  = $req = $this->ppe->get_request($nfrmno, $vorgno, $cyear, $cyear2, $nrunno);
        $this->views('ppe.webflow', $data);
    }

    public function approve($action, $runno, $cyear2)
    {
        $fmst   = $this->wf->getFormMaster('ST-PPE');
        $nfrmno = $fmst[0]->NNO;
        $vorgno = $fmst[0]->VORGNO;
        $cyear  = $fmst[0]->CYEAR;
        $apv    = $this->input->post('apv');
        $remark = $this->input->post('remark');
        $result = $this->doaction($action, $nfrmno, $vorgno, $cyear, $cyear2, $runno, $apv, $remark);
        echo $result;
    }
    public function update_confirm()
    {
        $nfrmno    = $this->input->post('NFRMNO');
        $vorgno    = $this->input->post('VORGNO');
        $cyear     = $this->input->post('CYEAR');
        $cyear2    = $this->input->post('CYEAR2');
        $nrunno    = $this->input->post('NRUNNO');
        $confirmed = $this->input->post('CONFIRMED');

        $where = [
            'NFRMNO' => $nfrmno,
            'VORGNO' => $vorgno,
            'CYEAR'  => $cyear,
            'CYEAR2' => $cyear2,
            'NRUNNO' => $nrunno
        ];
        $data  = [
            'CONFIRMED' => $confirmed,
        ];

        $this->ppe->update_ppe('UNIFORM_REQUEST', $where, $data);
    }

    public function getEmpFlow()
    {
        $nfrmno  = $this->input->post('NFRMNO');
        $vorgno  = $this->input->post('VORGNO');
        $cyear   = $this->input->post('CYEAR');
        $cyear2  = $this->input->post('CYEAR2');
        $nrunno  = $this->input->post('NRUNNO');
        $step    = $this->input->post('STEP');
        $cstepst = $this->input->post('STATUS');
        $data    = $this->ppe->getEmpFlow($nfrmno, $vorgno, $cyear, $cyear2, $nrunno, $step, $cstepst);
        echo json_encode($data);
    }

    public function getSize()
    {
        $catid = $this->input->post('catid');
        $data  = $this->ppe->get_uniform_by_cat($catid);
        echo json_encode($data);
    }

    public function getCategory()
    {
        $data = $this->ppe->get_category();
        echo json_encode($data);
    }

    public function getReason()
    {
        $data = $this->ppe->get_request_type();
        echo json_encode($data);
    }

    public function update_request()
    {
        $post = $this->input->post('formData');
        foreach ($post as $item) {
            $data  = [
                'UNIFORM_CATEGORY' => $item['category'],
                'UNIFORM_TYPE'     => $item['size'],
                'REQUEST_TYPE'     => $item['reason'],
                'QTY'              => $item['qty']
            ];
            $where = [
                'URD_ID' => $item['id']
            ];
            $this->ppe->update_ppe('UNIFORM_REQUEST_DETAIL', $where, $data);
        }
    }

    public function update_alocate()
    {
        $post = $this->input->post('formData');
        foreach ($post as $item) {
            $data  = [
                'PROD_ALOC' => 'PROD_ALOC' - $item['qty']
            ];
            $where = [
                'URD_ID' => $item['id']
            ];
        }
    }

    public function update_remark()
    {
        $data = $this->input->post('formData');
        foreach ($data as $item) {
            $this->ppe->updateRemarkFlow(
                $item['NFRMNO'],
                $item['VORGNO'],
                $item['CYEAR'],
                $item['CYEAR2'],
                $item['NRUNNO'],
                $item['remark']
            );
        }
    }
}
?>