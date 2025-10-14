<?php
use GuzzleHttp\Client;
defined('BASEPATH') OR exit('No direct script access allowed');
// require_once APPPATH.'controllers/_file.php';
require_once APPPATH.'controllers/_form.php';

class createForm extends MY_Controller {
    // use _File;
    use _Form;
    protected $client;
    public function __construct(){
		parent::__construct();
        $this->load->model('Area_model', 'area');
        $this->load->model('Webform_model', 'wf');
        $this->load->model('Form_model', 'frm');
        $this->load->model('electric_model', 'elec');
        $this->client = new Client(['verify' => false]);
    }

    /**
      * Job run Create safety electric form
      * @author  Mr.Sutthipong Tangmongkhoncharoen
      * @since   2025-03-31
      * @note    job run on server:amecweb4
      *          Task Scheduler: Safety -> 02-create electric form
      */
    public function getDataForm(){
        // exit($this->apiUrl);
        // exit(base_uri());
        try {
            $area = $this->area->getAreas();
            $frmMst =  $this->wf->getFormMaster('ST-ECS')[0];
            $res = 'Success!';
            $status  = false;
            $body[] = $this->messageTime('Begin');
            $body[] = $this->messageTime('Create Electric Form : '.date('Y-m-d'));
            $body[] = '<table style="border: 1px solid #333; width: 100%;">
                            <thead style="background-color: #e38300; color: #fff;">
                                <th>Date time</th>
                                <th>Owner</th>
                                <th>Area name</th>
                                <th>Message</th>
                            </thead>
                            <tbody>';
            if(!empty($area)){
                foreach($area as $a){
                    if(count($this->elec->checkFormCreate($a->AREA_ID)) == 0){
                        // สร้างฟอร์ม
                        $form = $this->create($frmMst->NNO, $frmMst->VORGNO, $frmMst->CYEAR, $a->AREA_EMPNO, $a->AREA_EMPNO, '', 1);
                        if($form['status']){
                            $form = (object)$form['message'];
                            $elecForm = $this->frm->getFormMaster(['CATEGORY' => $a->AREA_CATEGORY, 'TYPE' => $a->AREA_TYPE]);
                            $data = [
                                'NFRMNO' => $form->formtype,
                                'VORGNO' => $form->owner,
                                'CYEAR'  => $form->cyear,
                                'CYEAR2' => $form->cyear2,
                                'NRUNNO' => $form->runno,
                                'FORM_CATEGORY' => $a->AREA_CATEGORY,
                                'FORM_NO'       => !empty($elecForm) ? $elecForm[0]->FORMNO : '',
                                'FORM_AREA'     => $a->AREA_ID,
                                'FORM_REQUEST'  => $a->AREA_EMPNO,
                                'FORM_INPUT'    => $a->AREA_EMPNO,
                            ];
                            $status = $this->frm->insert('STY_FORM',$data);
                            if(!$status){
                                // ลบฟอร์ม
                                $del = $this->deleteForm($form->formtype, $form->owner, $form->cyear, $form->cyear2, $form->runno);
                                $message = $del['message'];
                            }else{
                                $message = 'Create success!';
                            }
                        }else{
                            $message = '<span style="color:red;">'.$form['message'].'</span>';
                        }
                    }else {
                        $message = '<span style="color:#e38300;">Form is already exists!</span>';
                    }
                    // $body[] = $this->messageTime($a->AREA_MANAGER . ' | ' . $a->AREA_NAME . ' | Message : ' . $message);
                    $body[] = "<tr>
                                    <td>".$this->messageTime()."</td>
                                    <td>$a->AREA_MANAGER</td>
                                    <td>$a->AREA_NAME</td>
                                    <td>$message</td>
                                </tr>";
                }
            }
            $body[] = '</tbody>
            </table>';
        } catch (Exception $e) {
            $res = 'Error';
            $body[] = $this->messageTime("Severity: Error --> {$e->getMessage()} {$e->getFile()} {$e->getLine()}");
        }finally{
            $body[] = $this->messageTime('End');
            // Send mail.
            $data = [
                'SUBJECT' => "[Job Create Electric Form]: {$res}.",
                'BODY'    => $body
            ];
            $mail = $this->mail->sendmail($data);
        }
    }
}