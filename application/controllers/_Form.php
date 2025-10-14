<?php
defined('BASEPATH') or exit('No direct script access allowed');

trait _Form{

    /**
    * @param number $no e.g. 5
    * @param number $orgNo e.g. 050601
    * @param number $y e.g. 20
    * @param number $y2 e.g. 2020
    * @param number $runNo  e.g. 2
    */
	public function toFormNumber($no, $orgNo, $y, $y2, $runNo)
	{
		$this->load->model('Webform_model', 'wf');
		$frmname = $this->wf->getFormName($no, $orgNo, $y); // ST-INP
		return $frmname[0]->VANAME.substr($y2,2,2)."-".str_pad($runNo, 6, "0", STR_PAD_LEFT); // ST-INP24-000001
	}


    public function getExtdata($no, $orgNo, $y, $y2, $runNo, $apv)
	{
		$this->load->model('Webform_model', 'wf');
		$extdata = $this->wf->getExtdata(array('NFRMNO' => $no , 'VORGNO' => $orgNo , 'CYEAR' => $y , 'CYEAR2' =>$y2 , 'NRUNNO' => $runNo), $apv);
		if(count($extdata) > 0){
			if(is_null($extdata[0]->CEXTDATA))
			{
				$ext = 0;
			}else{
				$ext = $extdata[0]->CEXTDATA;
			}
		}else{
			$ext = 0;
		}
		return $ext;
	}

    public function getMode($no, $orgNo, $y, $y2, $runNo, $apv)
    {
        $mode_add = "1";
        $mode_edit = "2";
        $mode_view = "3";
        $step_ready = "3";
        $this->load->model('Webform_model', 'wf');
        $rsf = $this->wf->customSelect("FLOW", array('NFRMNO' => $no, 'VORGNO' => $orgNo, 'CYEAR' => $y, 'CYEAR2' => $y2, 'NRUNNO' => $runNo));
        if(count($rsf) == 0)
        {
            return $mode_add;
        }else
        {
            $q = "select * From FLOW where NFRMNO = '".$no."' and VORGNO = '".$orgNo."' and CYEAR = '".$y."' and CYEAR2 = '".$y2."' and NRUNNO = '".$runNo."' and CSTEPST = '". $step_ready."' and (VAPVNO = '".$apv."' or VREPNO = '".$apv."')";
            $rsf = $this->db->query($q)->result();
            if(count($rsf) == 0)
            {
                return $mode_view;
            }else
            {
                return $mode_edit;
            }
        }
    }

    private function create($NNO, $VORGNO, $CYEAR, $req, $key, $remark, $draft){
        try{
            $response = $this->client->post("http://localhost/webservice/webflow/form/create", [
            // $response = $this->client->post(base_uri()."webservice/webflow/form/create", [
                'json' => [
                    "nfrmno" => $NNO,
                    "vorgno" => $VORGNO,
                    "cyear"  => $CYEAR,
                    "empno"  => $req,
                    "inputempno" => $key,
                    "remark" => $remark,
                    "draft"  => $draft,
                ]
            ]);
            $result = json_decode($response->getBody(), true);
            return $result;
        }catch(Exception $e){
            return array('status' => false, 'message' => 'Failed to create form', 'e' => $e);
        }
    }

    private function deleteForm($NFRMNO, $VORGNO, $CYEAR, $CYEAR2, $NRUNNO){
        try{
            $response = $this->client->post("http://localhost/webservice/webflow/form/deleteForm", [
            // $response = $this->client->post($this->$apiUrl.'/flow/deleteForm',[
                'json' => [
                    "nfrmno" => $NFRMNO,
                    "vorgno" => $VORGNO,
                    "cyear"  => $CYEAR,
                    "cyear2" => $CYEAR2,
                    "runno"  => $NRUNNO,
                ]
            ]);
            $result = json_decode($response->getBody(), true);
            return $result;
        }catch(Exception $e){
            return array('status' => false, 'message' => 'Failed to delete form');
        }
    }



}