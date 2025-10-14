<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class mailAlert extends MY_Controller {

    public function __construct(){
		parent::__construct();
        $this->load->model('Webform_model', 'wf');
    }
    
    
    /**
      * Job run Alert_Inspection_Form
      * @author  Mr.Sutthipong Tangmongkhoncharoen
      * @since   2025-03-07
      * @note    job run on server:amecweb4
      *          Task Scheduler: Safety -> 01-patrol mail alert
      */
    public function mail(){
        $data = $this->wf->getMailSemP();
        if(!empty($data)){
            foreach($data as $d){
                $mail['VIEW']    = 'layout/mail/mailAlert';
                $mail['SUBJECT'] = 'E-Form '.$d->FORMNO;
                // $mail['TO']      = 'sutthipongt@MitsubishiElevatorAsia.co.th';
                $mail['TO']      = $d->SRECMAIL;
                $mail['BODY']    = array(
                    "SAFETY INSPECTION REPORT BY AMSC’ COMMITTEE : Please approve/reject",
                    "1. Get into http://webflow/form",
                    "2. select 'Electronic forms'",
                    "3. select 'Waiting for approval'"
                );
                $this->mail->sendmail($mail);
            }
        }
    }
}